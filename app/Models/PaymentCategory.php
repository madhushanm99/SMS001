<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class PaymentCategory extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'parent_id',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PaymentCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(PaymentCategory::class, 'parent_id');
    }

    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    public function scopeParentCategories(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSubCategories(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeOrderedBySort(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isIncome(): bool
    {
        return $this->type === 'income';
    }

    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }

    public function isParentCategory(): bool
    {
        return is_null($this->parent_id);
    }

    public function isSubCategory(): bool
    {
        return !is_null($this->parent_id);
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        if ($this->isSubCategory() && $this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }

    public function getTotalAmount(): float
    {
        return $this->paymentTransactions()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getTransactionsCount(): int
    {
        return $this->paymentTransactions()->count();
    }

    public function getAllDescendantTransactions(): HasMany
    {
        $childrenIds = $this->children()->pluck('id')->toArray();
        $allIds = array_merge([$this->id], $childrenIds);

        return PaymentTransaction::whereIn('payment_category_id', $allIds);
    }

    // Static Methods
    public static function getIncomeOptions()
    {
        return static::active()
            ->income()
            ->with('parent')
            ->orderedBySort()
            ->get();
    }

    public static function getExpenseOptions()
    {
        return static::active()
            ->expense()
            ->with('parent')
            ->orderedBySort()
            ->get();
    }

    public static function getParentOptions(string $type): array
    {
        return static::active()
            ->where('type', $type)
            ->parentCategories()
            ->orderedBySort()
            ->pluck('name', 'id')
            ->toArray();
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }

    public static function getTypeOptions(): array
    {
        return [
            'income' => 'Income (Cash In)',
            'expense' => 'Expense (Cash Out)',
        ];
    }

    // Tree structure helpers
    public static function getTreeStructure(string $type): array
    {
        $categories = static::active()
            ->where('type', $type)
            ->orderedBySort()
            ->get()
            ->groupBy('parent_id');

        return static::buildTree($categories, null);
    }

    private static function buildTree($categories, $parentId): array
    {
        $tree = [];

        if (isset($categories[$parentId])) {
            foreach ($categories[$parentId] as $category) {
                $category->children_items = static::buildTree($categories, $category->id);
                $tree[] = $category;
            }
        }

        return $tree;
    }
}
