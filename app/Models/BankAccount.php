<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class BankAccount extends Model
{
    protected $fillable = [
        'account_name',
        'account_number',
        'bank_name',
        'bank_branch',
        'swift_code',
        'iban',
        'account_type',
        'current_balance',
        'opening_balance',
        'opening_date',
        'description',
        'is_active',
        'currency',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'opening_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function cashInTransactions(): HasMany
    {
        return $this->paymentTransactions()->where('type', 'cash_in');
    }

    public function cashOutTransactions(): HasMany
    {
        return $this->paymentTransactions()->where('type', 'cash_out');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('account_type', $type);
    }

    public function scopeByBank(Builder $query, string $bankName): Builder
    {
        return $query->where('bank_name', 'like', "%{$bankName}%");
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getFormattedBalance(): string
    {
        return number_format($this->current_balance, 2) . ' ' . $this->currency;
    }

    public function getBalanceDifference(): float
    {
        return $this->current_balance - $this->opening_balance;
    }

    public function getTotalCashIn(): float
    {
        return $this->cashInTransactions()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getTotalCashOut(): float
    {
        return $this->cashOutTransactions()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getNetFlow(): float
    {
        return $this->getTotalCashIn() - $this->getTotalCashOut();
    }

    public function updateBalance(): void
    {
        $netFlow = $this->getNetFlow();
        $this->update([
            'current_balance' => $this->opening_balance + $netFlow
        ]);
    }

    // Static Methods
    public static function getActiveOptions(): array
    {
        return static::active()
            ->orderBy('account_name')
            ->get()
            ->mapWithKeys(function ($account) {
                return [$account->id => "{$account->account_name} ({$account->account_number})"];
            })
            ->toArray();
    }

    public static function findByAccountNumber(string $accountNumber): ?self
    {
        return static::where('account_number', $accountNumber)->first();
    }

    // Account Types
    public static function getAccountTypes(): array
    {
        return [
            'checking' => 'Checking Account',
            'savings' => 'Savings Account',
            'current' => 'Current Account',
            'business' => 'Business Account',
        ];
    }
}
