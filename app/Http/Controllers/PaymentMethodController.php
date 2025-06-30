<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $paymentMethods = PaymentMethod::withCount('paymentTransactions')
            ->orderBy('name')
            ->get();

        // Handle AJAX/API requests
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $paymentMethods->map(function($method) {
                    return [
                        'id' => $method->id,
                        'name' => $method->name,
                        'code' => $method->code,
                        'is_active' => $method->is_active,
                        'requires_reference' => $method->requires_reference,
                    ];
                })
            ]);
        }

        return view('payment_methods.index', compact('paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('payment_methods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = $this->validatePaymentMethod($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            PaymentMethod::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
                'requires_reference' => $request->boolean('requires_reference', false),
            ]);

            return redirect()->route('payment-methods.index')
                ->with('success', 'Payment method created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating payment method: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod): View
    {
        $paymentMethod->loadCount('paymentTransactions');
        
        $recentTransactions = $paymentMethod->paymentTransactions()
            ->with(['paymentCategory', 'customer', 'supplier'])
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        $summary = [
            'total_amount' => $paymentMethod->getTotalAmount(),
            'transaction_count' => $paymentMethod->getTransactionsCount(),
        ];

        return view('payment_methods.show', compact(
            'paymentMethod', 
            'recentTransactions',
            'summary'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $paymentMethod): View
    {
        return view('payment_methods.edit', compact('paymentMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $validator = $this->validatePaymentMethod($request, $paymentMethod->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $paymentMethod->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'requires_reference' => $request->boolean('requires_reference'),
            ]);

            return redirect()->route('payment-methods.show', $paymentMethod)
                ->with('success', 'Payment method updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating payment method: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        // Check if payment method has transactions
        if ($paymentMethod->paymentTransactions()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete payment method that has associated transactions.');
        }

        try {
            $paymentMethod->delete();
            return redirect()->route('payment-methods.index')
                ->with('success', 'Payment method deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting payment method: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(PaymentMethod $paymentMethod): JsonResponse
    {
        try {
            $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);
            
            $status = $paymentMethod->is_active ? 'activated' : 'deactivated';
            return response()->json([
                'success' => "Payment method {$status} successfully",
                'is_active' => $paymentMethod->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating status'], 500);
        }
    }

    /**
     * Get active payment methods for API
     */
    public function getActive(): JsonResponse
    {
        $methods = PaymentMethod::active()
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'requires_reference']);

        return response()->json($methods);
    }

    // Private helper methods

    private function validatePaymentMethod(Request $request, $ignoreId = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'name' => 'required|string|max:255|unique:payment_methods,name' . ($ignoreId ? ",$ignoreId" : ''),
            'code' => 'required|string|max:10|unique:payment_methods,code' . ($ignoreId ? ",$ignoreId" : ''),
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'requires_reference' => 'boolean',
        ];

        return Validator::make($request->all(), $rules);
    }
}
