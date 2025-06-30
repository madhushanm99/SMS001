<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'requires_reference',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_reference' => 'boolean',
    ];

    // Relationships
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeRequiringReference(Builder $query): Builder
    {
        return $query->where('requires_reference', true);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function requiresReference(): bool
    {
        return $this->requires_reference;
    }

    public function getTransactionsCount(): int
    {
        return $this->paymentTransactions()->count();
    }

    public function getTotalAmount(): float
    {
        return $this->paymentTransactions()
            ->where('status', 'completed')
            ->sum('amount');
    }

    // Static Methods
    public static function getActiveOptions(): array
    {
        return static::active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
