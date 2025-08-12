<?php

namespace App\Http\Controllers;

use App\Models\GRN;
use App\Models\GRNItem;
use App\Models\Products;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\RecomputeSupplierTotals;


class GRNController extends Controller
{
    protected function sessionKey(): string
    {
        return 'temp_grn_items_' . auth()->id();
    }

    public function index(Request $request)
    {
        $query = GRN::where('status', 1)
            ->with([
                'items',
                'supplier',
                'paymentTransactions' => function($q) {
                    $q->where('status', 'completed');
                }
            ]);

        // Filters
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function($q) use ($search) {
                $q->where('grn_no', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhere('po_No', 'like', "%{$search}%");
            });
        }

        if ($request->filled('supplier')) {
            $query->where('supp_Cus_ID', $request->get('supplier'));
        }

        if ($request->filled('date_from')) {
            $query->where('grn_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('grn_date', '<=', $request->get('date_to'));
        }

        $grns = $query->orderByDesc('grn_id')->paginate(10)->withQueryString();

        // Calculate payment status for each GRN
        foreach ($grns as $grn) {
            $grn->total_amount = $grn->items->sum('line_total');
            $grn->paid_amount = $grn->getTotalPayments();
            $grn->outstanding_amount = $grn->getOutstandingAmount();
            $grn->payment_status = $grn->getPaymentStatus();
        }

        // Get payment methods and bank accounts for payment prompt
        $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'is_active']);

        $bankAccounts = \App\Models\BankAccount::orderBy('account_name')
            ->get(['id', 'account_name', 'bank_name']);

        $paymentCategories = \App\Models\PaymentCategory::where('is_active', true)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'type']);

        // Suppliers for filter dropdown
        $suppliers = \App\Models\Supplier::orderBy('Supp_Name')->get(['Supp_CustomID', 'Supp_Name']);

        return view('grns.index', compact('grns', 'paymentMethods', 'bankAccounts', 'paymentCategories', 'suppliers'));
    }

    public function create()
    {
        session()->forget($this->sessionKey());
        return view('grns.create');
    }

    public function storeTempItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string',
            'qty' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);
        $item = Products::where('item_ID', $request->item_id)->first();
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Invalid item']);
        }

        $key = $this->sessionKey();
        $items = session()->get($key, []);

        $discount = $request->discount ?? 0;
        $subtotal = $request->qty * $item->sales_Price;
        $discountAmount = ($subtotal * $discount) / 100;
        $lineTotal = $subtotal - $discountAmount;

        // Prevent duplicates: merge if exists
        foreach ($items as &$existing) {
            if ($existing['item_ID'] === $item->item_ID) {
                $existing['qty'] += $request->qty;
                $existingSubtotal = $existing['qty'] * $existing['price'];
                $existingDiscountAmount = ($existingSubtotal * $existing['discount']) / 100;
                $existing['line_total'] = $existingSubtotal - $existingDiscountAmount;
                session([$key => $items]);
                return response()->json(['success' => true, 'items' => $items]);
            }
        }

        $items[] = [
            'item_ID' => $item->item_ID,
            'description' => $item->item_Name,
            'price' => $item->sales_Price,
            'qty' => $request->qty,
            'discount' => $discount,
            'line_total' => $lineTotal,
        ];

        session([$key => $items]);

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function removeTempItem(Request $request)
    {
        $request->validate(['index' => 'required|integer|min:0']);
        $key = $this->sessionKey();
        $items = session()->get($key, []);
        if (isset($items[$request->index])) {
            unset($items[$request->index]);
            session([$key => array_values($items)]);
        }

        return response()->json(['success' => true, 'items' => session($key)]);
    }

    public function fetchTempItems()
    {
        return response()->json([
            'success' => true,
            'items' => session()->get($this->sessionKey(), [])
        ]);
    }

    public function importFromPO(Request $request)
    {
        $request->validate([
            'po_auto_id' => 'required|exists:po,po_Auto_ID',
        ]);

        $poId = (int) $request->po_auto_id;
        $po = DB::table('po')->where('po_Auto_ID', $poId)->first();
        if (!$po) {
            return response()->json(['success' => false, 'message' => 'PO not found'], 404);
        }

        // Load PO items
        $poItems = DB::table('po__Item')
            ->join('item', 'po__Item.item_ID', '=', 'item.item_ID')
            ->where('po__Item.po_Auto_ID', $poId)
            ->select('po__Item.item_ID', 'item.item_Name', 'po__Item.price', 'po__Item.qty', 'po__Item.line_Total')
            ->get();

        $items = [];
        foreach ($poItems as $row) {
            $qty = (int) $row->qty;
            $price = (float) $row->price;
            $lineTotal = $qty * $price;
            $items[] = [
                'item_ID' => $row->item_ID,
                'description' => $row->item_Name,
                'price' => $price,
                'qty' => $qty,
                'discount' => 0,
                'line_total' => $lineTotal,
            ];
        }

        // Replace current temp items with PO items
        session([$this->sessionKey() => $items]);

        return response()->json([
            'success' => true,
            'items' => $items,
            'supplier_id' => $po->supp_Cus_ID,
            'po_no' => $po->po_No,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'grn_date' => 'required|date',
            'supp_Cus_ID' => 'required|string',
        ]);

        $items = session()->get($this->sessionKey(), []);
        if (empty($items)) {
            return back()->with('error', 'Add at least one item to GRN.');
        }

        DB::beginTransaction();

        try {
            $grnNo = GRN::generateGRNNumber();

            $grnId = GRN::insertGetId([
                'grn_no' => $grnNo,
                'grn_date' => $request->grn_date,
                'po_Auto_ID' => $request->po_Auto_ID,
                'po_No' => $request->po_No,
                'supp_Cus_ID' => $request->supp_Cus_ID,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'received_by' => $request->received_by,
                'note' => $request->note,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update supplier aggregates (Last_GRN, Total_Orders) atomically with GRN creation
            $supplierForAggregates = \App\Models\Supplier::where('Supp_CustomID', $request->supp_Cus_ID)->first();
            if ($supplierForAggregates) {
                $supplierForAggregates->update(['Last_GRN' => $grnNo]);
                $supplierForAggregates->increment('Total_Orders');
            }

            foreach ($items as $item) {
                GRNItem::create([
                    'grn_id' => $grnId,
                    'item_ID' => $item['item_ID'],
                    'item_Name' => $item['description'],
                    'qty_received' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'line_total' => $item['line_total'],
                ]);

                // Update stock quantity
                Stock::increase($item['item_ID'], $item['qty']);

                // Calculate discounted unit cost and update stock cost if higher
                $discountedUnitCost = $item['qty'] > 0 ? $item['line_total'] / $item['qty'] : $item['price'];
                Stock::updateCostIfHigher($item['item_ID'], $discountedUnitCost);

                // Update item price if GRN price is higher
                Products::updatePriceIfHigher($item['item_ID'], $item['price']);
            }

            // If this GRN is linked to a PO, mark that PO as received
            if (!empty($request->po_Auto_ID)) {
                DB::table('po')
                    ->where('po_Auto_ID', $request->po_Auto_ID)
                    ->update([
                        'orderStatus' => 'received',
                        'updated_at' => now(),
                    ]);
            }

            session()->forget($this->sessionKey());

            DB::commit();

            // Recompute supplier aggregates in background
            if ($supplierForAggregates) {
                RecomputeSupplierTotals::dispatch($supplierForAggregates->Supp_CustomID);
            }

            // Calculate totals for payment prompt
            $totalAmount = collect($items)->sum('line_total');

            // Get supplier information and any available credit from unpaid purchase returns
            $supplier = \App\Models\Supplier::where('Supp_CustomID', $request->supp_Cus_ID)->first();
            $availableCredit = $supplier ? $supplier->getOutstandingPurchaseReturnCredit() : 0.0;

            // Net outstanding after applying available credit (but do not go below zero)
            $netOutstanding = max(0, $totalAmount - $availableCredit);

            session()->flash('grn_created', [
                'grn_id' => $grnId,
                'grn_no' => $grnNo,
                'supplier_id' => $request->supp_Cus_ID,
                'supplier_name' => $supplier->Supp_Name ?? 'Unknown Supplier',
                'total_amount' => $totalAmount,
                'available_credit' => $availableCredit,
                'outstanding_amount' => $netOutstanding,
                'prompt_payment' => true
            ]);

            return redirect()->route('grns.index')->with('success', 'GRN created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create GRN: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $grn = GRN::findOrFail($id);

        // Prevent editing if fully paid
        if ($grn->getOutstandingAmount() <= 0) {
            return redirect()->route('grns.index')->with('error', 'Fully paid GRNs cannot be edited.');
        }

        $items = GRNItem::where('grn_id', $id)->get();

        $tempItems = [];
        foreach ($items as $item) {
            $tempItems[] = [
                'item_ID' => $item->item_ID,
                'description' => $item->item_Name,
                'price' => $item->price,
                'qty' => $item->qty_received,
                'discount' => $item->discount ?? 0,
                'line_total' => $item->line_total,
            ];
        }

        session([$this->sessionKey() => $tempItems]);

        return view('grns.edit', compact('grn'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'grn_date' => 'required|date',
            'supp_Cus_ID' => 'required|string',
        ]);

        $items = session()->get($this->sessionKey(), []);
        if (empty($items)) {
            return back()->with('error', 'No items to save. Please add at least one.');
        }

        DB::beginTransaction();

        try {
            $grn = GRN::findOrFail($id);

            // Prevent update if fully paid
            if ($grn->getOutstandingAmount() <= 0) {
                DB::rollBack();
                return redirect()->route('grns.index')->with('error', 'Fully paid GRNs cannot be edited.');
            }

            // Rollback old stock
            $oldItems = GRNItem::where('grn_id', $id)->get();
            foreach ($oldItems as $item) {
                $currentStock = Stock::where('item_ID', $item->item_ID)->value('quantity');
                if ($currentStock < $item->qty_received) {
                    return back()->with('error', "Not enough stock to rollback for item {$item->item_ID}.");
                }
            }

            foreach ($oldItems as $item) {
                Stock::decrease($item->item_ID, $item->qty_received);
            }

            GRNItem::where('grn_id', $id)->delete();

            // Save updated items
            foreach ($items as $item) {
                GRNItem::create([
                    'grn_id' => $id,
                    'item_ID' => $item['item_ID'],
                    'item_Name' => $item['description'],
                    'qty_received' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'line_total' => $item['line_total'],
                ]);

                // Update stock quantity
                Stock::increase($item['item_ID'], $item['qty']);

                // Calculate discounted unit cost and update stock cost if higher
                $discountedUnitCost = $item['qty'] > 0 ? $item['line_total'] / $item['qty'] : $item['price'];
                Stock::updateCostIfHigher($item['item_ID'], $discountedUnitCost);

                // Update item price if GRN price is higher
                Products::updatePriceIfHigher($item['item_ID'], $item['price']);
            }

            // Update GRN master
            $grn->update([
                'grn_date' => $request->grn_date,
                'po_Auto_ID' => $request->po_Auto_ID,
                'po_No' => $request->po_No,
                'supp_Cus_ID' => $request->supp_Cus_ID,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'received_by' => $request->received_by,
                'note' => $request->note,
                'updated_at' => now(),
            ]);

            session()->forget($this->sessionKey());
            DB::commit();

            return redirect()->route('grns.index')->with('success', 'GRN updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update GRN: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $grn = GRN::findOrFail($id);

        if (!$grn->status) {
            return back()->with('error', 'GRN already deleted.');
        }

        $items = GRNItem::where('grn_id', $id)->get();

        foreach ($items as $item) {
            $currentStock = Stock::where('item_ID', $item->item_ID)->value('quantity');
            if ($currentStock < $item->qty_received) {
                return back()->with('error', "Cannot delete. Not enough stock to reverse for item {$item->item_ID}.");
            }
        }

        foreach ($items as $item) {
            Stock::decrease($item->item_ID, $item->qty_received);
        }

        $grn->update(['status' => false]);

        return back()->with('success', 'GRN deleted and stock rolled back.');
    }


    public function pdf($id)
    {
        $grn = GRN::findOrFail($id);
        $items = GRNItem::where('grn_id', $id)->get();
        $supplier = DB::table('suppliers')->where('Supp_CustomID', $grn->supp_Cus_ID)->first();
        $pdf = Pdf::loadView('grns.pdf', compact('grn', 'items', 'supplier'));
        return $pdf->stream("GRN-{$grn->grn_no}.pdf");
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'grn_id' => 'required|exists:grn,grn_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payment_category_id' => 'required|exists:payment_categories,id',
            'description' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:100',
        ]);

        try {
            $grn = \App\Models\GRN::findOrFail($request->grn_id);
            $supplier = \App\Models\Supplier::where('Supp_CustomID', $grn->supp_Cus_ID)->first();

            // Calculate GRN total and outstanding amount using the model methods
            $grnTotal = $grn->items->sum('line_total');
            $outstandingAmount = $grn->getOutstandingAmount();

            if ($request->amount > $outstandingAmount) {
                return response()->json([
                    'success' => false,
                    'message' => "Payment amount ({$request->amount}) cannot exceed outstanding amount ({$outstandingAmount})"
                ]);
            }

            DB::beginTransaction();

            $transaction = \App\Models\PaymentTransaction::create([
                'transaction_no' => \App\Models\PaymentTransaction::generateTransactionNumber(),
                'type' => 'cash_out',
                'amount' => $request->amount,
                'transaction_date' => now(),
                'description' => $request->description ?: "Payment for GRN {$grn->grn_no}",
                'payment_method_id' => $request->payment_method_id,
                'bank_account_id' => $request->bank_account_id,
                'payment_category_id' => $request->payment_category_id,
                'supplier_id' => $grn->supp_Cus_ID,
                'purchase_order_id' => $grn->po_Auto_ID,
                'grn_id' => $grn->grn_id,
                'reference_no' => $request->reference_no,
                'status' => 'completed',
                'created_by' => auth()->user()->name ?? 'System',
                'approved_by' => auth()->user()->name ?? 'System',
                'approved_at' => now(),
            ]);

            // Update bank account balance if specified
            if ($request->bank_account_id) {
                $bankAccount = \App\Models\BankAccount::find($request->bank_account_id);
                if ($bankAccount) {
                    $bankAccount->decrement('current_balance', $request->amount);
                }
            }

            DB::commit();

            // Keep supplier totals in sync (background)
            if ($supplier) {
                RecomputeSupplierTotals::dispatch($supplier->Supp_CustomID);
            }

            // Refresh the GRN to get updated payment information
            $grn->refresh();
            $remainingBalance = $grn->getOutstandingAmount();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully!',
                'payment_id' => $transaction->id,
                'transaction_no' => $transaction->transaction_no,
                'remaining_balance' => $remainingBalance,
                'is_fully_paid' => $remainingBalance <= 0
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error recording payment: ' . $e->getMessage()
            ]);
        }
    }
}
