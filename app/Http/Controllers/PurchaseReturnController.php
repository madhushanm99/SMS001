<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\GRN;
use App\Models\GRNItem;
use App\Models\Stock;
use App\Models\PaymentTransaction;
use App\Models\PaymentMethod;
use App\Models\PaymentCategory;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseReturn::with(['items', 'paymentTransactions', 'supplier']);
        if ($request->filled('supplier')) {
            $query->where('supp_Cus_ID', $request->supplier);
        }

        if ($request->filled('grn')) {
            $query->where('grn_no', $request->grn);
        }

        $returns = $query->latest()->get();
        $suppliers = DB::table('suppliers')->get();
        $grns = DB::table('grn')->select('grn_no')->get();

        // Load payment-related data for the payment modal
        $payment_methods = PaymentMethod::where('is_active', true)->get();
        $bank_accounts = BankAccount::where('is_active', true)->get();
        $payment_categories = PaymentCategory::where('is_active', true)->get();

        return view('purchase_returns.index', compact('returns', 'suppliers', 'grns', 'payment_methods', 'bank_accounts', 'payment_categories'));
    }
    public function create()
    {
        $grns = GRN::where('status', true)->get();
        $grn_items_by_grn_id = [];

        foreach ($grns as $grn) {
            $items = GRNItem::where('grn_id', $grn->grn_id)->get()->map(function ($item) {
                $stockQty = Stock::where('item_ID', $item->item_ID)->value('quantity') ?? 0;
                return [
                    'item_ID' => $item->item_ID,
                    'item_Name' => $item->item_Name,
                    'qty_received' => $item->qty_received,
                    'price' => $item->price,
                    'stock_qty' => $stockQty,
                ];
            });

            $grn_items_by_grn_id[$grn->grn_id] = $items;
        }

        return view('purchase_returns.create', compact('grns', 'grn_items_by_grn_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grn_id' => 'required|exists:grn,grn_id',
            'supp_Cus_ID' => 'required',
            'items' => 'required|array|min:1',
            'items.*.item_ID' => 'required|string',
            'items.*.qty_returned' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Stock validation
            foreach ($request->items as $item) {
                $stock = Stock::where('item_ID', $item['item_ID'])->value('quantity') ?? 0;
                if ($item['qty_returned'] > $stock) {
                    return back()->with('error', "Cannot return {$item['item_ID']}, not enough stock.");
                }
            }

            $grn = GRN::findOrFail($request->grn_id);

            $return = PurchaseReturn::create([
                'return_no' => PurchaseReturn::generateReturnNo(),
                'grn_id' => $grn->grn_id,
                'grn_no' => $grn->grn_no,
                'supp_Cus_ID' => $request->supp_Cus_ID,
                'note' => $request->note,
                'returned_by' => auth()->user()->name ?? 'system',
                'status' => true,
            ]);

            foreach ($request->items as $item) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'item_ID' => $item['item_ID'],
                    'item_Name' => $item['item_Name'],
                    'qty_returned' => $item['qty_returned'],
                    'price' => $item['price'],
                    'line_total' => $item['qty_returned'] * $item['price'],
                    'reason' => $item['reason'] ?? null,
                ]);

                // Reduce stock
                Stock::where('item_ID', $item['item_ID'])->decrement('quantity', $item['qty_returned']);
            }

            DB::commit();
            
            // Check if request wants payment prompt
            if ($request->has('show_payment_prompt')) {
                $paymentMethods = PaymentMethod::where('is_active', true)->get();
                $bankAccounts = BankAccount::where('is_active', true)->get();
                $paymentCategories = PaymentCategory::where('is_active', true)->get();
                
                return response()->json([
                    'success' => true,
                    'show_payment_prompt' => true,
                    'purchase_return_id' => $return->id,
                    'return_no' => $return->return_no,
                    'total_amount' => $return->getTotalAmount(),
                    'supplier_name' => $return->supplier->Supp_Name ?? 'Unknown',
                    'payment_methods' => $paymentMethods,
                    'bank_accounts' => $bankAccounts,
                    'payment_categories' => $paymentCategories,
                ]);
            }
            
            return redirect()->route('purchase_returns.index')->with('success', 'Purchase return saved.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving return: ' . $e->getMessage());
        }
    }

    public function createPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_category_id' => 'required|exists:payment_categories,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'description' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:100',
        ]);

        try {
            $purchaseReturn = PurchaseReturn::findOrFail($id);
            $outstanding = $purchaseReturn->getOutstandingAmount();
            
            if ($request->amount > $outstanding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refund amount cannot exceed outstanding amount of LKR ' . number_format($outstanding, 2)
                ], 422);
            }

            $payment = PaymentTransaction::create([
                'type' => 'cash_in', // Purchase return refund is cash in (money received from supplier)
                'amount' => $request->amount,
                'transaction_date' => now()->toDateString(),
                'description' => $request->description ?: "Refund for Purchase Return {$purchaseReturn->return_no}",
                'reference_no' => $request->reference_no,
                'payment_method_id' => $request->payment_method_id,
                'bank_account_id' => $request->bank_account_id,
                'payment_category_id' => $request->payment_category_id,
                'supplier_id' => $purchaseReturn->supp_Cus_ID,
                'purchase_return_id' => $purchaseReturn->id,
                'status' => 'completed',
            ]);

            $newOutstanding = $purchaseReturn->getOutstandingAmount();
            $paymentStatus = $purchaseReturn->getPaymentStatus();

            return response()->json([
                'success' => true,
                'message' => 'Refund recorded successfully!',
                'payment_id' => $payment->id,
                'transaction_no' => $payment->transaction_no,
                'new_outstanding' => $newOutstanding,
                'payment_status' => $paymentStatus,
                'is_fully_paid' => $purchaseReturn->isFullyPaid(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording refund: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $pr = PurchaseReturn::findOrFail($id);
        if (!$pr->status) {
            return back()->with('error', 'Already deleted.');
        }

        $pr->update(['status' => false]);
        return back()->with('success', 'Return soft-deleted. Stock not restored.');
    }

    public function pdf($id)
    {
        $return = PurchaseReturn::with('items')->findOrFail($id);
        $supplier = DB::table('suppliers')->where('Supp_CustomID', $return->supp_Cus_ID)->first();
        $pdf = Pdf::loadView('purchase_returns.pdf', compact('return', 'supplier'));
        return $pdf->stream("PR-{$return->return_no}.pdf");
    }

}
