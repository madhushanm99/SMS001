<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\PaymentMethod;
use App\Models\BankAccount;
use App\Models\PaymentCategory;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SalesInvoice;
use App\Models\Po;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = PaymentTransaction::with([
            'paymentMethod',
            'bankAccount',
            'paymentCategory',
            'customer',
            'supplier'
        ]);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_no', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest('transaction_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        // Get filter options
        $paymentMethods = PaymentMethod::active()->get();
        $statuses = PaymentTransaction::getStatusOptions();
        $types = PaymentTransaction::getTypeOptions();

        // Get summary data
        $summary = $this->getSummaryData($request);

        return view('payment_transactions.index', compact(
            'transactions',
            'paymentMethods',
            'statuses',
            'types',
            'summary'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        // Get dropdown options
        $paymentMethods = PaymentMethod::active()->get();
        $bankAccounts = BankAccount::active()->get();
        $customers = Customer::where('status', true)->orderBy('name')->get();
        $suppliers = Supplier::orderBy('Supp_Name')->get();

        // Get categories based on transaction type
        $type = $request->get('type', 'cash_out');
        $categories = $type === 'cash_in'
            ? PaymentCategory::getIncomeOptions()
            : PaymentCategory::getExpenseOptions();

        // Pre-fill data if linking to invoice/PO
        $linkedData = $this->getLinkedEntityData($request);

        return view('payment_transactions.create', compact(
            'paymentMethods',
            'bankAccounts',
            'categories',
            'customers',
            'suppliers',
            'type',
            'linkedData'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = $this->validateTransaction($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $transaction = PaymentTransaction::create([
                'type' => $request->type,
                'amount' => $request->amount,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description,
                'reference_no' => $request->reference_no,
                'payment_method_id' => $request->payment_method_id,
                'bank_account_id' => $request->bank_account_id,
                'payment_category_id' => $request->payment_category_id,
                'customer_id' => $request->customer_id,
                'supplier_id' => $request->supplier_id,
                'sales_invoice_id' => $request->sales_invoice_id,
                'purchase_order_id' => $request->purchase_order_id,
                'status' => $request->status ?? 'completed',
                'notes' => $request->notes,
                'created_by' => Auth::user()->name ?? Auth::user()->email,
            ]);

            // Handle file attachments if any (offload heavy work)
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                // Store minimally now; enqueue post-processing (virus scan, thumbnails, cloud upload)
                $attachments = $this->handleAttachments($files);
                $transaction->update(['attachments' => $attachments]);
                \App\Jobs\ProcessPaymentAttachments::dispatch($transaction->id);
            }

            // Auto-complete if not pending approval
            if ($transaction->status === 'completed') {
                $this->completeTransaction($transaction);
            }

            DB::commit();

            // Optional redirect back to quick forms
            if ($request->get('redirect') === 'quick-cash-in') {
                return redirect()->route('payment-transactions.quick-cash-in')
                    ->with('success', 'Payment recorded successfully. You can add another.');
            }
            if ($request->get('redirect') === 'quick-cash-out') {
                return redirect()->route('payment-transactions.quick-cash-out')
                    ->with('success', 'Payment recorded successfully. You can add another.');
            }

            return redirect()->route('payment-transactions.show', $transaction)
                ->with('success', 'Payment transaction created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating transaction: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentTransaction $paymentTransaction): View
    {
        $paymentTransaction->load([
            'paymentMethod',
            'bankAccount',
            'paymentCategory.parent',
            'customer',
            'supplier',
            'salesInvoice',
            'purchaseOrder'
        ]);

        return view('payment_transactions.show', compact('paymentTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentTransaction $paymentTransaction): View
    {
        // Only allow editing of draft and pending transactions
        if (!in_array($paymentTransaction->status, ['draft', 'pending'])) {
            abort(403, 'This transaction cannot be edited.');
        }

        $paymentMethods = PaymentMethod::active()->get();
        $bankAccounts = BankAccount::active()->get();
        $customers = Customer::where('status', true)->orderBy('name')->get();
        $suppliers = Supplier::orderBy('Supp_Name')->get();

        $categories = $paymentTransaction->type === 'cash_in'
            ? PaymentCategory::getIncomeOptions()
            : PaymentCategory::getExpenseOptions();

        return view('payment_transactions.edit', compact(
            'paymentTransaction',
            'paymentMethods',
            'bankAccounts',
            'categories',
            'customers',
            'suppliers'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentTransaction $paymentTransaction): RedirectResponse
    {
        // Only allow editing of draft and pending transactions
        if (!in_array($paymentTransaction->status, ['draft', 'pending'])) {
            return redirect()->back()->with('error', 'This transaction cannot be edited.');
        }

        $validator = $this->validateTransaction($request, $paymentTransaction->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $paymentTransaction->update([
                'type' => $request->type,
                'amount' => $request->amount,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description,
                'reference_no' => $request->reference_no,
                'payment_method_id' => $request->payment_method_id,
                'bank_account_id' => $request->bank_account_id,
                'payment_category_id' => $request->payment_category_id,
                'customer_id' => $request->customer_id,
                'supplier_id' => $request->supplier_id,
                'sales_invoice_id' => $request->sales_invoice_id,
                'purchase_order_id' => $request->purchase_order_id,
                'notes' => $request->notes,
            ]);

            // Handle file attachments (offload heavy work)
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                $attachments = $this->handleAttachments($files);
                $paymentTransaction->update(['attachments' => $attachments]);
                \App\Jobs\ProcessPaymentAttachments::dispatch($paymentTransaction->id);
            }

            DB::commit();

            return redirect()->route('payment-transactions.show', $paymentTransaction)
                ->with('success', 'Payment transaction updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating transaction: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentTransaction $paymentTransaction): RedirectResponse
    {
        // Only allow deletion of draft transactions
        if ($paymentTransaction->status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft transactions can be deleted.');
        }

        try {
            $paymentTransaction->delete();
            return redirect()->route('payment-transactions.index')
                ->with('success', 'Payment transaction deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting transaction: ' . $e->getMessage());
        }
    }

    /**
     * Approve a transaction
     */
    public function approve(PaymentTransaction $paymentTransaction): JsonResponse
    {
        if (!$paymentTransaction->canBeApproved()) {
            return response()->json(['error' => 'Transaction cannot be approved'], 400);
        }

        try {
            $paymentTransaction->approve();
            return response()->json(['success' => 'Transaction approved successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error approving transaction'], 500);
        }
    }

    /**
     * Complete a transaction
     */
    public function complete(PaymentTransaction $paymentTransaction): JsonResponse
    {
        try {
            $result = $paymentTransaction->complete();

            if ($result) {
                $this->completeTransaction($paymentTransaction);
                return response()->json(['success' => 'Transaction completed successfully']);
            }

            return response()->json(['error' => 'Transaction cannot be completed'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error completing transaction'], 500);
        }
    }

    /**
     * Cancel a transaction
     */
    public function cancel(PaymentTransaction $paymentTransaction): JsonResponse
    {
        if (!$paymentTransaction->canBeCancelled()) {
            return response()->json(['error' => 'Transaction cannot be cancelled'], 400);
        }

        try {
            $paymentTransaction->cancel();
            return response()->json(['success' => 'Transaction cancelled successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error cancelling transaction'], 500);
        }
    }

    /**
     * Get dashboard data
     */
    public function dashboard(): View
    {
        $summary = PaymentTransaction::getDashboardSummary();

        $recentTransactions = PaymentTransaction::with([
            'paymentMethod',
            'paymentCategory',
            'customer',
            'supplier'
        ])
        ->recent(7)
        ->latest('transaction_date')
        ->limit(10)
        ->get();

        $pendingTransactions = PaymentTransaction::with([
            'paymentMethod',
            'paymentCategory'
        ])
        ->pending()
        ->latest('transaction_date')
        ->limit(5)
        ->get();

        return view('payment_transactions.dashboard', compact(
            'summary',
            'recentTransactions',
            'pendingTransactions'
        ));
    }

    /**
     * Quick cash in form
     */
    public function quickCashIn(): View
    {
        $paymentMethods = PaymentMethod::active()->get();
        $bankAccounts = BankAccount::active()->get();
        $categories = PaymentCategory::getIncomeOptions();
        $customers = Customer::where('status', true)->orderBy('name')->limit(20)->get();

        $recentTransactions = PaymentTransaction::with(['paymentMethod'])
            ->cashIn()
            ->latest('transaction_date')
            ->latest('id')
            ->limit(10)
            ->get();

        return view('payment_transactions.quick_cash_in', compact(
            'paymentMethods',
            'bankAccounts',
            'categories',
            'customers',
            'recentTransactions'
        ));
    }

    /**
     * Quick cash out form
     */
    public function quickCashOut(): View
    {
        $paymentMethods = PaymentMethod::active()->get();
        $bankAccounts = BankAccount::active()->get();
        $categories = PaymentCategory::getExpenseOptions();
        $suppliers = Supplier::orderBy('Supp_Name')->limit(20)->get();

        $recentTransactions = PaymentTransaction::with(['paymentMethod'])
            ->cashOut()
            ->latest('transaction_date')
            ->latest('id')
            ->limit(10)
            ->get();

        return view('payment_transactions.quick_cash_out', compact(
            'paymentMethods',
            'bankAccounts',
            'categories',
            'suppliers',
            'recentTransactions'
        ));
    }

    /**
     * Search customers for AJAX
     */
    public function searchCustomers(Request $request): JsonResponse
    {
        $search = $request->get('q');

        $customers = Customer::where('status', true)
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('custom_id', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['custom_id as id', 'name', 'custom_id', 'phone']);

        return response()->json($customers);
    }

    /**
     * Search suppliers for AJAX
     */
    public function searchSuppliers(Request $request): JsonResponse
    {
        $search = $request->get('q');

        $suppliers = Supplier::where(function($query) use ($search) {
                $query->where('Supp_Name', 'like', "%{$search}%")
                      ->orWhere('Supp_CustomID', 'like', "%{$search}%")
                      ->orWhere('Company_Name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['Supp_CustomID as id', 'Supp_Name as name', 'Company_Name', 'Supp_CustomID']);

        return response()->json($suppliers);
    }

    /**
     * Search sales invoices for AJAX
     */
    public function searchInvoices(Request $request): JsonResponse
    {
        $search = $request->get('q');

        $invoices = SalesInvoice::with('customer')
            ->where(function($query) use ($search) {
                $query->where('invoice_no', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%")
                      ->orWhere('customer_id', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'text' => sprintf('INV #%s - %s (Outstanding: %0.2f)',
                        $invoice->invoice_no ?? $invoice->id,
                        optional($invoice->customer)->name ?? 'N/A',
                        $invoice->getOutstandingAmount()
                    ),
                ];
            });

        return response()->json($invoices);
    }

    /**
     * Search purchase orders for AJAX
     */
    public function searchPurchaseOrders(Request $request): JsonResponse
    {
        $search = $request->get('q');

        $orders = Po::where(function($query) use ($search) {
                $query->where('po_No', 'like', "%{$search}%")
                      ->orWhere('po_Auto_ID', 'like', "%{$search}%")
                      ->orWhere('supp_Cus_ID', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($po) {
                return [
                    'id' => $po->po_Auto_ID,
                    'text' => sprintf('PO #%s - %s',
                        $po->po_No ?? $po->po_Auto_ID,
                        $po->supp_Cus_ID
                    ),
                ];
            });

        return response()->json($orders);
    }

    // Private helper methods

    private function validateTransaction(Request $request, $ignoreId = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'type' => 'required|in:cash_in,cash_out',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_category_id' => 'required|exists:payment_categories,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'reference_no' => 'nullable|string|max:100',
            'customer_id' => 'nullable|exists:customers,custom_id',
            'supplier_id' => 'nullable|exists:suppliers,Supp_CustomID',
                    'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
        'purchase_order_id' => 'nullable|exists:po,po_Auto_ID',
        'invoice_return_id' => 'nullable|exists:invoice_returns,id',
        'status' => 'nullable|in:draft,pending,approved,completed',
        'notes' => 'nullable|string',
        'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        // Add conditional validations
        $rules = array_merge($rules, [
            'reference_no' => [
                'nullable',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    $paymentMethod = PaymentMethod::find($request->payment_method_id);
                    if ($paymentMethod && $paymentMethod->requires_reference && empty($value)) {
                        $fail('Reference number is required for this payment method.');
                    }
                }
            ],
            'bank_account_id' => [
                'nullable',
                'exists:bank_accounts,id',
                function ($attribute, $value, $fail) use ($request) {
                    $paymentMethod = PaymentMethod::find($request->payment_method_id);
                    if ($paymentMethod && $paymentMethod->code !== 'CASH' && empty($value)) {
                        $fail('Bank account is required for non-cash payments.');
                    }
                }
            ]
        ]);

        return Validator::make($request->all(), $rules);
    }

    private function getSummaryData(Request $request): array
    {
        $query = PaymentTransaction::completed();

        // Apply same filters as main query
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $cashIn = (clone $query)->cashIn()->sum('amount');
        $cashOut = (clone $query)->cashOut()->sum('amount');

        return [
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'net_flow' => $cashIn - $cashOut,
            'total_transactions' => PaymentTransaction::count(),
            'pending_count' => PaymentTransaction::pending()->count(),
        ];
    }

    private function getLinkedEntityData(Request $request): array
    {
        $data = [];

        if ($request->filled('sales_invoice_id')) {
            $invoice = SalesInvoice::with('customer')->find($request->sales_invoice_id);
            if ($invoice) {
                $data = [
                    'entity_type' => 'sales_invoice',
                    'entity' => $invoice,
                    'amount' => $invoice->getOutstandingAmount(),
                    'type' => 'cash_in',
                    'customer_id' => $invoice->customer_id,
                    'description' => "Payment for Invoice #{$invoice->invoice_no}",
                ];
            }
        }

        if ($request->filled('purchase_order_id')) {
            $po = Po::with('supplier')->find($request->purchase_order_id);
            if ($po) {
                $data = [
                    'entity_type' => 'purchase_order',
                    'entity' => $po,
                    'amount' => $po->getOutstandingAmount(),
                    'type' => 'cash_out',
                    'supplier_id' => $po->supp_Cus_ID,
                    'description' => "Payment for PO #{$po->po_No}",
                ];
            }
        }

        return $data;
    }

    private function handleAttachments(array $files): array
    {
        $attachments = [];

        foreach ($files as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('payment_attachments', $filename, 'public');

            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
        }

        return $attachments;
    }

    private function completeTransaction(PaymentTransaction $transaction): void
    {
        // Update related entity balances if applicable
        if ($transaction->customer_id) {
            $transaction->customer->updateCreditBalance();
        }

        if ($transaction->supplier_id) {
            $transaction->supplier->updateTotalSpent();
        }

        // Update bank account balance
        if ($transaction->bank_account_id) {
            $transaction->bankAccount->updateBalance();
        }
    }
}
