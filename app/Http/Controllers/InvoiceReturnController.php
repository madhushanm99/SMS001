<?php

namespace App\Http\Controllers;

use App\Models\InvoiceReturn;
use App\Models\InvoiceReturnItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Customer;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceReturnController extends Controller
{
    protected function sessionKey(): string
    {
        return 'temp_invoice_return_items_' . auth()->id();
    }

    public function index(Request $request)
    {
        // Check if user is admin or manager
        if (!in_array(auth()->user()->usertype ?? 'user', ['admin', 'manager'])) {
            return redirect()->back()->with('error', 'You do not have permission to access invoice returns.');
        }

        $query = InvoiceReturn::query();
        
        // Add search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('return_no', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('return_date', [$request->from_date, $request->to_date]);
        }

        $returns = $query->with(['customer', 'salesInvoice'])
                         ->orderByDesc('created_at')
                         ->paginate(10);

        if ($request->ajax()) {
            return view('invoice_returns.table', compact('returns'))->render();
        }

        return view('invoice_returns.index', compact('returns'));
    }

    public function selectInvoice()
    {
        // Check if user is admin or manager
        if (!in_array(auth()->user()->usertype ?? 'user', ['admin', 'manager'])) {
            return redirect()->back()->with('error', 'You do not have permission to access invoice returns.');
        }

        session()->forget($this->sessionKey());
        return view('invoice_returns.select_invoice');
    }

    public function searchInvoices(Request $request)
    {
        $term = $request->get('term', '');
        $invoices = SalesInvoice::with('customer')
                              ->where('status', 'finalized') // Only finalized invoices can be returned
                              ->where(function($query) use ($term) {
                                  $query->where('invoice_no', 'like', "%{$term}%")
                                        ->orWhereHas('customer', function($subQ) use ($term) {
                                            $subQ->where('name', 'like', "%{$term}%")
                                                 ->orWhere('phone', 'like', "%{$term}%")
                                                 ->orWhere('custom_id', 'like', "%{$term}%");
                                        });
                              })
                              ->limit(10)
                              ->get();

        return response()->json($invoices->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'text' => $invoice->invoice_no . ' - ' . $invoice->customer->name . ' (Rs. ' . number_format($invoice->grand_total, 2) . ')',
                'invoice_no' => $invoice->invoice_no,
                'customer_name' => $invoice->customer->name,
                'customer_id' => $invoice->customer_id,
                'grand_total' => $invoice->grand_total,
                'invoice_date' => $invoice->invoice_date->format('Y-m-d')
            ];
        }));
    }

    public function createReturn($invoiceId)
    {
        // Check if user is admin or manager
        if (!in_array(auth()->user()->usertype ?? 'user', ['admin', 'manager'])) {
            return redirect()->back()->with('error', 'You do not have permission to access invoice returns.');
        }

        $invoice = SalesInvoice::with(['customer', 'items', 'paymentTransactions' => function($query) {
            $query->where('status', 'completed')->where('type', 'cash_in');
        }, 'returns'])->findOrFail($invoiceId);
        
        if ($invoice->status !== 'finalized') {
            return redirect()->route('invoice_returns.select')
                           ->with('error', 'Only finalized invoices can be returned');
        }

        // Get payment options
        $paymentMethods = \App\Models\PaymentMethod::active()->get();
        $bankAccounts = \App\Models\BankAccount::active()->get();
        $paymentCategories = \App\Models\PaymentCategory::where('type', 'expense')->get();

        // Calculate payment summary
        $totalPaid = $invoice->getTotalPayments();
        $totalReturns = $invoice->returns()->sum('total_amount');
        $availableForReturn = $totalPaid - $totalReturns;

        session()->forget($this->sessionKey());
        
        return view('invoice_returns.create', compact(
            'invoice', 
            'paymentMethods', 
            'bankAccounts', 
            'paymentCategories',
            'totalPaid',
            'totalReturns', 
            'availableForReturn'
        ));
    }

    public function addReturnItem(Request $request)
    {
        $request->validate([
            'invoice_item_id' => 'required|integer',
            'qty_returned' => 'required|integer|min:1',
            'return_reason' => 'nullable|string',
        ]);

        $invoiceItem = SalesInvoiceItem::findOrFail($request->invoice_item_id);
        
        if ($request->qty_returned > $invoiceItem->qty) {
            return response()->json([
                'success' => false, 
                'message' => 'Return quantity cannot exceed original quantity (' . $invoiceItem->qty . ')'
            ]);
        }

        $key = $this->sessionKey();
        $items = session()->get($key, []);

        // Check if item already exists in return session
        $existingIndex = null;
        foreach ($items as $index => $existing) {
            if ($existing['invoice_item_id'] === $invoiceItem->id) {
                $existingIndex = $index;
                break;
            }
        }

        $lineTotal = ($invoiceItem->unit_price * $request->qty_returned) - 
                    (($invoiceItem->unit_price * $request->qty_returned * $invoiceItem->discount) / 100);

        if ($existingIndex !== null) {
            // Update existing item
            $newQty = $items[$existingIndex]['qty_returned'] + $request->qty_returned;
            if ($newQty > $invoiceItem->qty) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Total return quantity cannot exceed original quantity (' . $invoiceItem->qty . ')'
                ]);
            }
            $items[$existingIndex]['qty_returned'] = $newQty;
            $items[$existingIndex]['line_total'] = ($invoiceItem->unit_price * $newQty) - 
                                                  (($invoiceItem->unit_price * $newQty * $invoiceItem->discount) / 100);
        } else {
            // Add new item
            $items[] = [
                'invoice_item_id' => $invoiceItem->id,
                'item_id' => $invoiceItem->item_id,
                'item_name' => $invoiceItem->item_name,
                'original_qty' => $invoiceItem->qty,
                'qty_returned' => $request->qty_returned,
                'unit_price' => $invoiceItem->unit_price,
                'discount' => $invoiceItem->discount,
                'line_total' => $lineTotal,
                'return_reason' => $request->return_reason ?? '',
            ];
        }

        session([$key => $items]);

        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => collect($items)->sum('line_total')
        ]);
    }

    public function removeReturnItem(Request $request)
    {
        $key = $this->sessionKey();
        $items = session()->get($key, []);
        
        // Remove item from session
        $items = array_filter($items, function($item) use ($request) {
            return $item['invoice_item_id'] !== (int)$request->invoice_item_id;
        });
        
        session([$key => array_values($items)]);
        
        return response()->json([
            'success' => true,
            'items' => array_values($items),
            'total' => collect($items)->sum('line_total')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:sales_invoices,id',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payment_category_id' => 'required|exists:payment_categories,id',
        ]);

        $items = session()->get($this->sessionKey(), []);
        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'No items added for return']);
        }

        $invoice = SalesInvoice::with('customer')->findOrFail($request->invoice_id);
        
        // Calculate total return amount
        $totalReturnAmount = collect($items)->sum('line_total');
        
        // Validate return amount against paid amount
        $totalPaidAmount = $invoice->getTotalPayments();
        $existingReturns = $invoice->returns()->sum('total_amount');
        $availableForReturn = $totalPaidAmount - $existingReturns;
        
        if ($totalReturnAmount > $availableForReturn) {
            return response()->json([
                'success' => false, 
                'message' => sprintf(
                    'Return amount (Rs. %.2f) cannot exceed available refund amount (Rs. %.2f). Total paid: Rs. %.2f, Previous returns: Rs. %.2f',
                    $totalReturnAmount,
                    $availableForReturn,
                    $totalPaidAmount,
                    $existingReturns
                )
            ]);
        }

        DB::beginTransaction();
        try {
            $return = InvoiceReturn::create([
                'return_no' => InvoiceReturn::generateReturnNo(),
                'sales_invoice_id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'customer_id' => $invoice->customer_id,
                'return_date' => now()->toDateString(),
                'total_amount' => collect($items)->sum('line_total'),
                'reason' => $request->reason,
                'notes' => $request->notes,
                'processed_by' => auth()->user()->name ?? 'System',
                'status' => 'completed',
            ]);

            foreach ($items as $index => $item) {
                InvoiceReturnItem::create([
                    'invoice_return_id' => $return->id,
                    'line_no' => $index + 1,
                    'item_id' => $item['item_id'],
                    'item_name' => $item['item_name'],
                    'qty_returned' => $item['qty_returned'],
                    'original_qty' => $item['original_qty'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'line_total' => $item['line_total'],
                    'return_reason' => $item['return_reason'],
                ]);

                            // Increase stock for returned items
            Stock::increase($item['item_id'], $item['qty_returned']);
        }

        // Create refund payment transaction
        $refundTransaction = \App\Models\PaymentTransaction::create([
            'transaction_no' => \App\Models\PaymentTransaction::generateTransactionNumber(),
            'type' => 'cash_out',
            'amount' => $return->total_amount,
            'transaction_date' => now(),
            'description' => "Refund for Return #{$return->return_no} (Invoice #{$invoice->invoice_no})",
            'payment_method_id' => $request->payment_method_id,
            'bank_account_id' => $request->bank_account_id,
            'payment_category_id' => $request->payment_category_id,
            'customer_id' => $invoice->customer_id,
            'sales_invoice_id' => $invoice->id,
            'invoice_return_id' => $return->id,
            'reference_no' => $return->return_no,
            'status' => 'completed',
            'created_by' => auth()->user()->name ?? 'System',
            'approved_by' => auth()->user()->name ?? 'System',
            'approved_at' => now(),
        ]);

        // Update bank account balance if specified
        if ($request->bank_account_id) {
            $bankAccount = \App\Models\BankAccount::find($request->bank_account_id);
            if ($bankAccount) {
                $bankAccount->decrement('current_balance', $return->total_amount);
            }
        }

        DB::commit();
        session()->forget($this->sessionKey());

        return response()->json([
            'success' => true,
            'message' => 'Invoice return and refund processed successfully',
            'return_id' => $return->id,
            'refund_transaction_id' => $refundTransaction->id,
            'redirect_url' => route('invoice_returns.index')
        ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error processing return: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        // Check if user is admin or manager
        if (!in_array(auth()->user()->usertype ?? 'user', ['admin', 'manager'])) {
            return redirect()->back()->with('error', 'You do not have permission to view invoice returns.');
        }

        $return = InvoiceReturn::with(['customer', 'salesInvoice', 'items'])->findOrFail($id);
        return view('invoice_returns.show', compact('return'));
    }

    public function pdf($id)
    {
        // Check if user is admin or manager
        if (!in_array(auth()->user()->usertype ?? 'user', ['admin', 'manager'])) {
            return redirect()->back()->with('error', 'You do not have permission to view invoice returns.');
        }

        $return = InvoiceReturn::with(['customer', 'salesInvoice', 'items'])->findOrFail($id);
        
        $pdf = Pdf::loadView('invoice_returns.pdf', compact('return'));
        
        return $pdf->stream("return-{$return->return_no}.pdf");
    }

    public function getSessionItems()
    {
        $sessionKey = $this->sessionKey();
        $items = session()->get($sessionKey, []);
        $total = collect($items)->sum('line_total');
        
        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => $total
        ]);
    }
} 