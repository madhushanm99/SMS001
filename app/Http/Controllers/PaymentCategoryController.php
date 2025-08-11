<?php

namespace App\Http\Controllers;

use App\Models\PaymentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class PaymentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = PaymentCategory::withCount('paymentTransactions')
            ->with('parent')
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Handle AJAX/API requests
        if ($request->wantsJson()) {
            $filteredCategories = $categories;

            // Filter by type if specified
            if ($request->has('type')) {
                $filteredCategories = $categories->where('type', $request->type);
            }

            return response()->json([
                'success' => true,
                'data' => $filteredCategories->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'code' => $category->code,
                        'type' => $category->type,
                        'parent_id' => $category->parent_id,
                        'is_active' => $category->is_active,
                        'full_name' => $category->getFullName(),
                    ];
                })->values()
            ]);
        }

        // Analytics for charts and table badges
        $analytics = [
            'usage_counts' => $categories->mapWithKeys(function ($cat) {
                return [$cat->id => $cat->payment_transactions_count ?? 0];
            })->toArray(),
            'total_amounts' => [],
        ];

        // Basic chart datasets
        $chartData = [
            'usage' => $categories->map(function ($cat) {
                return [
                    'name' => $cat->name,
                    'count' => $cat->payment_transactions_count ?? 0,
                ];
            })->values(),
            'volume' => $categories->map(function ($cat) {
                return [
                    'name' => $cat->name,
                    'type' => $cat->type,
                    'amount' => 0,
                ];
            })->values(),
        ];

        return view('payment_categories.index', compact(
            'categories',
            'analytics',
            'chartData'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $type = $request->get('type', 'expense');
        $parentCategories = PaymentCategory::where('type', $type)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('payment_categories.create', compact('type', 'parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = $this->validatePaymentCategory($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get next sort order
            $sortOrder = PaymentCategory::where('type', $request->type)
                ->where('parent_id', $request->parent_id)
                ->max('sort_order') + 1;

            PaymentCategory::create([
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'parent_id' => $request->parent_id,
                'description' => $request->description,
                'sort_order' => $sortOrder,
                'is_active' => $request->boolean('is_active', true),
                'color' => $request->color,
            ]);

            return redirect()->route('payment-categories.index')
                ->with('success', 'Payment category created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating payment category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentCategory $paymentCategory): View
    {
        $paymentCategory->loadCount('paymentTransactions');
        $paymentCategory->load(['parent', 'children.paymentTransactions']);

        // Get recent transactions
        $recentTransactions = $paymentCategory->paymentTransactions()
            ->with(['paymentMethod', 'customer', 'supplier', 'bankAccount'])
            ->latest('transaction_date')
            ->limit(20)
            ->get();

        // Get summary statistics
        $summary = [
            'total_amount' => $paymentCategory->getTotalAmount(),
            'transaction_count' => $paymentCategory->getTransactionsCount(),
            'avg_transaction' => $paymentCategory->getAverageTransaction(),
            'last_transaction' => $paymentCategory->getLastTransaction(),
            'monthly_trend' => $paymentCategory->getMonthlyTrend(),
        ];

        return view('payment_categories.show', compact(
            'paymentCategory',
            'recentTransactions',
            'summary'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentCategory $paymentCategory): View
    {
        $parentCategories = PaymentCategory::where('type', $paymentCategory->type)
            ->whereNull('parent_id')
            ->where('id', '!=', $paymentCategory->id)
            ->orderBy('name')
            ->get();

        return view('payment_categories.edit', compact(
            'paymentCategory',
            'parentCategories'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentCategory $paymentCategory): RedirectResponse
    {
        $validator = $this->validatePaymentCategory($request, $paymentCategory->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $paymentCategory->update([
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'parent_id' => $request->parent_id,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'color' => $request->color,
            ]);

            return redirect()->route('payment-categories.show', $paymentCategory)
                ->with('success', 'Payment category updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating payment category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentCategory $paymentCategory): RedirectResponse
    {
        // Check if category has transactions
        if ($paymentCategory->paymentTransactions()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete category that has associated transactions.');
        }

        // Check if category has children
        if ($paymentCategory->children()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete category that has subcategories. Delete subcategories first.');
        }

        try {
            $paymentCategory->delete();
            return redirect()->route('payment-categories.index')
                ->with('success', 'Payment category deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting payment category: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(PaymentCategory $paymentCategory): JsonResponse
    {
        try {
            $paymentCategory->update(['is_active' => !$paymentCategory->is_active]);

            $status = $paymentCategory->is_active ? 'activated' : 'deactivated';
            return response()->json([
                'success' => "Payment category {$status} successfully",
                'is_active' => $paymentCategory->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating status'], 500);
        }
    }

    /**
     * Get categories by type for API
     */
    public function getByType(Request $request): JsonResponse
    {
        $type = $request->get('type', 'expense');

        $categories = PaymentCategory::where('type', $type)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'parent_id', 'full_name']);

        return response()->json($categories);
    }

    /**
     * Get hierarchical tree for API
     */
    public function getTree(Request $request): JsonResponse
    {
        $type = $request->get('type');

        $query = PaymentCategory::where('is_active', true)
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($type) {
            $query->where('type', $type);
        }

        $categories = $query->get();

        return response()->json($categories);
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:payment_categories,id',
            'categories.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            foreach ($request->categories as $categoryData) {
                PaymentCategory::where('id', $categoryData['id'])
                    ->update(['sort_order' => $categoryData['sort_order']]);
            }

            return response()->json(['success' => 'Sort order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating sort order'], 500);
        }
    }

    /**
     * Get category analytics
     */
    public function analytics(PaymentCategory $paymentCategory): JsonResponse
    {
        $period = request()->get('period', 12); // months

        $analytics = [
            'monthly_data' => $paymentCategory->getMonthlyData($period),
            'top_customers' => $paymentCategory->getTopCustomers(5),
            'top_suppliers' => $paymentCategory->getTopSuppliers(5),
            'payment_methods' => $paymentCategory->getPaymentMethodBreakdown(),
            'trend_analysis' => $paymentCategory->getTrendAnalysis($period),
        ];

        return response()->json($analytics);
    }

    /**
     * Merge categories
     */
    public function merge(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'source_id' => 'required|exists:payment_categories,id',
            'target_id' => 'required|exists:payment_categories,id|different:source_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            $sourceCategory = PaymentCategory::findOrFail($request->source_id);
            $targetCategory = PaymentCategory::findOrFail($request->target_id);

            // Check if both categories are of same type
            if ($sourceCategory->type !== $targetCategory->type) {
                return response()->json(['error' => 'Categories must be of same type'], 400);
            }

            // Move all transactions from source to target
            $sourceCategory->paymentTransactions()
                ->update(['payment_category_id' => $targetCategory->id]);

            // Move children categories
            $sourceCategory->children()
                ->update(['parent_id' => $targetCategory->id]);

            // Delete source category
            $sourceCategory->delete();

            return response()->json([
                'success' => "Category '{$sourceCategory->name}' merged into '{$targetCategory->name}' successfully",
                'merged_transactions' => $sourceCategory->paymentTransactions()->count()
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error merging categories'], 500);
        }
    }

    // Private helper methods

    private function validatePaymentCategory(Request $request, $ignoreId = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:payment_categories,code' . ($ignoreId ? ",$ignoreId" : ''),
            'type' => 'required|in:income,expense',
            'parent_id' => 'nullable|exists:payment_categories,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'color' => 'nullable|string|max:7', // hex color
        ];

        // Add validation to prevent parent being child of itself
        if ($ignoreId) {
            $rules['parent_id'] = [
                'nullable',
                'exists:payment_categories,id',
                function ($attribute, $value, $fail) use ($ignoreId) {
                    if ($value == $ignoreId) {
                        $fail('Category cannot be parent of itself.');
                    }

                    // Check for circular reference
                    $category = PaymentCategory::find($value);
                    while ($category && $category->parent_id) {
                        if ($category->parent_id == $ignoreId) {
                            $fail('This would create a circular reference.');
                            break;
                        }
                        $category = $category->parent;
                    }
                }
            ];
        }

        return Validator::make($request->all(), $rules);
    }
}
