<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bankAccounts = BankAccount::withCount('paymentTransactions')
            ->orderBy('account_name')
            ->get();

        // Handle AJAX/API requests
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $bankAccounts->map(function($account) {
                    return [
                        'id' => $account->id,
                        'account_name' => $account->account_name,
                        'account_number' => $account->account_number,
                        'bank_name' => $account->bank_name,
                        'account_type' => $account->account_type,
                        'current_balance' => $account->current_balance,
                        'currency' => $account->currency,
                        'is_active' => $account->is_active,
                    ];
                })
            ]);
        }

        // Summary for cards expected by the view
        $summary = [
            'total_balance' => (float) $bankAccounts->sum('current_balance'),
            'active_accounts' => (int) $bankAccounts->where('is_active', true)->count(),
            'unreconciled_count' => (int) $bankAccounts->filter(function ($account) {
                // Consider unreconciled if computed balance differs from stored current_balance
                $computed = (float) ($account->opening_balance + $account->getNetFlow());
                return round($computed - (float) $account->current_balance, 2) !== 0.00;
            })->count(),
        ];

        // Recent activity across accounts (last 10 transactions involving bank accounts)
        $recentActivity = PaymentTransaction::with(['bankAccount'])
            ->whereNotNull('bank_account_id')
            ->latest('transaction_date')
            ->latest('id')
            ->limit(10)
            ->get();

        // Chart data: balances per account
        $chartData = [
            'balances' => $bankAccounts->map(function ($account) {
                return [
                    'account_name' => $account->account_name,
                    'balance' => (float) $account->current_balance,
                ];
            })->values(),
        ];

        return view('bank_accounts.index', compact(
            'bankAccounts',
            'summary',
            'recentActivity',
            'chartData'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $accountTypes = BankAccount::getAccountTypeOptions();
        $currencies = BankAccount::getCurrencyOptions();

        return view('bank_accounts.create', compact('accountTypes', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = $this->validateBankAccount($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            BankAccount::create([
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'account_type' => $request->account_type,
                'opening_balance' => $request->opening_balance ?? 0,
                'current_balance' => $request->opening_balance ?? 0,
                'currency' => $request->currency ?? 'LKR',
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
                'bank_code' => $request->bank_code,
                'branch_name' => $request->branch_name,
            ]);

            return redirect()->route('bank-accounts.index')
                ->with('success', 'Bank account created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating bank account: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount): View
    {
        $bankAccount->loadCount('paymentTransactions');

        // Get recent transactions
        $recentTransactions = $bankAccount->paymentTransactions()
            ->with(['paymentMethod', 'paymentCategory', 'customer', 'supplier'])
            ->latest('transaction_date')
            ->limit(20)
            ->get();

        // Get cash flow data for chart
        $cashFlowData = $bankAccount->getCashFlowData(30); // Last 30 days

        // Get summary statistics
        $summary = [
            'current_balance' => $bankAccount->current_balance,
            'opening_balance' => $bankAccount->opening_balance,
            'cash_in_total' => $bankAccount->getCashInTotal(),
            'cash_out_total' => $bankAccount->getCashOutTotal(),
            'transaction_count' => $bankAccount->paymentTransactions()->count(),
            'last_transaction_date' => $bankAccount->getLastTransactionDate(),
        ];

        return view('bank_accounts.show', compact(
            'bankAccount',
            'recentTransactions',
            'cashFlowData',
            'summary'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount): View
    {
        $accountTypes = BankAccount::getAccountTypeOptions();
        $currencies = BankAccount::getCurrencyOptions();

        return view('bank_accounts.edit', compact(
            'bankAccount',
            'accountTypes',
            'currencies'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        $validator = $this->validateBankAccount($request, $bankAccount->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $bankAccount->update([
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'account_type' => $request->account_type,
                'currency' => $request->currency,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'bank_code' => $request->bank_code,
                'branch_name' => $request->branch_name,
            ]);

            return redirect()->route('bank-accounts.show', $bankAccount)
                ->with('success', 'Bank account updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating bank account: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount): RedirectResponse
    {
        // Check if bank account has transactions
        if ($bankAccount->paymentTransactions()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete bank account that has associated transactions.');
        }

        try {
            $bankAccount->delete();
            return redirect()->route('bank-accounts.index')
                ->with('success', 'Bank account deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting bank account: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(BankAccount $bankAccount): JsonResponse
    {
        try {
            $bankAccount->update(['is_active' => !$bankAccount->is_active]);

            $status = $bankAccount->is_active ? 'activated' : 'deactivated';
            return response()->json([
                'success' => "Bank account {$status} successfully",
                'is_active' => $bankAccount->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating status'], 500);
        }
    }

    /**
     * Reconcile bank account
     */
    public function reconcile(BankAccount $bankAccount): View
    {
        // Get unreconciled transactions
        $unreconciledTransactions = $bankAccount->paymentTransactions()
            ->completed()
            ->where('is_reconciled', false)
            ->with(['paymentMethod', 'paymentCategory'])
            ->latest('transaction_date')
            ->get();

        $summary = [
            'unreconciled_count' => $unreconciledTransactions->count(),
            'unreconciled_amount' => $unreconciledTransactions->sum('amount'),
            'current_balance' => $bankAccount->current_balance,
        ];

        return view('bank_accounts.reconcile', compact(
            'bankAccount',
            'unreconciledTransactions',
            'summary'
        ));
    }

    /**
     * Update balance
     */
    public function updateBalance(Request $request, BankAccount $bankAccount): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'new_balance' => 'required|numeric',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            $oldBalance = $bankAccount->current_balance;
            $newBalance = $request->new_balance;

            $bankAccount->update([
                'current_balance' => $newBalance,
                'last_reconciled_at' => now(),
            ]);

            // Log the balance adjustment if there's a difference
            $difference = $newBalance - $oldBalance;
            if ($difference != 0) {
                // Create adjustment transaction
                $bankAccount->paymentTransactions()->create([
                    'type' => $difference > 0 ? 'cash_in' : 'cash_out',
                    'amount' => abs($difference),
                    'transaction_date' => now(),
                    'description' => 'Balance Adjustment - ' . ($request->notes ?? 'Manual reconciliation'),
                    'payment_method_id' => 1, // Assuming adjustment method exists
                    'payment_category_id' => 1, // Assuming adjustment category exists
                    'status' => 'completed',
                    'is_reconciled' => true,
                    'notes' => $request->notes,
                ]);
            }

            return response()->json([
                'success' => 'Balance updated successfully',
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
                'difference' => $difference,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating balance'], 500);
        }
    }

    /**
     * Get active bank accounts for API
     */
    public function getActive(): JsonResponse
    {
        $accounts = BankAccount::active()
            ->orderBy('account_name')
            ->get(['id', 'account_name', 'bank_name', 'account_number', 'current_balance', 'currency']);

        return response()->json($accounts);
    }

    /**
     * Get bank account statement
     */
    public function statement(Request $request, BankAccount $bankAccount): View
    {
        $validator = Validator::make($request->all(), [
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $dateFrom = $request->date_from ?? now()->subMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        $transactions = $bankAccount->paymentTransactions()
            ->with(['paymentMethod', 'paymentCategory', 'customer', 'supplier'])
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        // Calculate running balance
        $runningBalance = $bankAccount->opening_balance;
        $transactions = $transactions->map(function ($transaction) use (&$runningBalance) {
            if ($transaction->type === 'cash_in') {
                $runningBalance += $transaction->amount;
            } else {
                $runningBalance -= $transaction->amount;
            }
            $transaction->running_balance = $runningBalance;
            return $transaction;
        });

        $summary = [
            'opening_balance' => $bankAccount->opening_balance,
            'closing_balance' => $runningBalance,
            'total_in' => $transactions->where('type', 'cash_in')->sum('amount'),
            'total_out' => $transactions->where('type', 'cash_out')->sum('amount'),
            'net_flow' => $transactions->where('type', 'cash_in')->sum('amount') -
                         $transactions->where('type', 'cash_out')->sum('amount'),
        ];

        return view('bank_accounts.statement', compact(
            'bankAccount',
            'transactions',
            'summary',
            'dateFrom',
            'dateTo'
        ));
    }

    // Private helper methods

    private function validateBankAccount(Request $request, $ignoreId = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50|unique:bank_accounts,account_number' . ($ignoreId ? ",$ignoreId" : ''),
            'bank_name' => 'required|string|max:255',
            'account_type' => 'required|in:savings,current,credit,loan',
            'opening_balance' => 'nullable|numeric',
            'currency' => 'required|string|max:3',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'bank_code' => 'nullable|string|max:20',
            'branch_name' => 'nullable|string|max:255',
        ];

        return Validator::make($request->all(), $rules);
    }
}
