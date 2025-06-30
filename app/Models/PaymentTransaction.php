<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'transaction_no',
        'type',
        'amount',
        'transaction_date',
        'transaction_time',
        'description',
        'reference_no',
        'payment_method_id',
        'bank_account_id',
        'payment_category_id',
        'customer_id',
        'supplier_id',
        'sales_invoice_id',
        'purchase_order_id',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'transaction_time' => 'datetime',
        'approved_at' => 'datetime',
        'attachments' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_no)) {
                $transaction->transaction_no = static::generateTransactionNumber();
            }
            if (empty($transaction->created_by) && Auth::check()) {
                $transaction->created_by = Auth::user()->name ?? Auth::user()->email;
            }
            if (empty($transaction->transaction_time)) {
                $transaction->transaction_time = now();
            }
        });

        static::updating(function ($transaction) {
            if (Auth::check()) {
                $transaction->updated_by = Auth::user()->name ?? Auth::user()->email;
            }
        });
    }

    // Relationships
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function paymentCategory(): BelongsTo
    {
        return $this->belongsTo(PaymentCategory::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'custom_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'Supp_CustomID');
    }

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(Po::class, 'purchase_order_id', 'po_Auto_ID');
    }

    // Scopes
    public function scopeCashIn(Builder $query): Builder
    {
        return $query->where('type', 'cash_in');
    }

    public function scopeCashOut(Builder $query): Builder
    {
        return $query->where('type', 'cash_out');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeByPaymentMethod(Builder $query, $methodId): Builder
    {
        return $query->where('payment_method_id', $methodId);
    }

    public function scopeByCategory(Builder $query, $categoryId): Builder
    {
        return $query->where('payment_category_id', $categoryId);
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('transaction_date', '>=', now()->subDays($days));
    }

    // Helper Methods
    public function isCashIn(): bool
    {
        return $this->type === 'cash_in';
    }

    public function isCashOut(): bool
    {
        return $this->type === 'cash_out';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getFormattedAmount(): string
    {
        $prefix = $this->isCashIn() ? '+' : '-';
        return $prefix . number_format($this->amount, 2);
    }

    public function getFormattedAmountWithCurrency(): string
    {
        $currency = $this->bankAccount ? $this->bankAccount->currency : 'LKR';
        return $this->getFormattedAmount() . ' ' . $currency;
    }

    public function getTypeLabel(): string
    {
        return $this->isCashIn() ? 'Cash In' : 'Cash Out';
    }

    public function getStatusLabel(): string
    {
        return ucfirst($this->status);
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'approved' => 'info',
            'pending' => 'warning',
            'draft' => 'secondary',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    public function approve(?string $approverName = null): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        return $this->update([
            'status' => 'approved',
            'approved_by' => $approverName ?? (Auth::user()->name ?? Auth::user()->email),
            'approved_at' => now(),
        ]);
    }

    public function complete(): bool
    {
        if (!in_array($this->status, ['approved', 'pending'])) {
            return false;
        }

        $result = $this->update(['status' => 'completed']);
        
        // Update bank account balance if applicable
        if ($result && $this->bank_account_id) {
            $this->bankAccount->updateBalance();
        }

        return $result;
    }

    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        return $this->update(['status' => 'cancelled']);
    }

    // Static Methods
    public static function generateTransactionNumber(): string
    {
        $prefix = 'TXN';
        $date = now()->format('Ymd');
        $lastTransaction = static::whereDate('created_at', today())
            ->latest('transaction_no')
            ->first();
        
        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_no, -4);
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public static function getStatusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function getTypeOptions(): array
    {
        return [
            'cash_in' => 'Cash In (Income)',
            'cash_out' => 'Cash Out (Expense)',
        ];
    }

    // Summary Methods
    public static function getTotalCashIn($startDate = null, $endDate = null): float
    {
        $query = static::cashIn()->completed();
        
        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }
        
        return $query->sum('amount');
    }

    public static function getTotalCashOut($startDate = null, $endDate = null): float
    {
        $query = static::cashOut()->completed();
        
        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }
        
        return $query->sum('amount');
    }

    public static function getNetCashFlow($startDate = null, $endDate = null): float
    {
        return static::getTotalCashIn($startDate, $endDate) - static::getTotalCashOut($startDate, $endDate);
    }

    public static function getDashboardSummary(): array
    {
        $today = now()->toDateString();
        $thisMonth = [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()];
        
        return [
            'today' => [
                'cash_in' => static::getTotalCashIn($today, $today),
                'cash_out' => static::getTotalCashOut($today, $today),
                'net_flow' => static::getNetCashFlow($today, $today),
            ],
            'this_month' => [
                'cash_in' => static::getTotalCashIn($thisMonth[0], $thisMonth[1]),
                'cash_out' => static::getTotalCashOut($thisMonth[0], $thisMonth[1]),
                'net_flow' => static::getNetCashFlow($thisMonth[0], $thisMonth[1]),
            ],
            'pending_count' => static::pending()->count(),
            'recent_count' => static::recent(7)->count(),
        ];
    }
}
