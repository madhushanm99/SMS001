<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'customer_id',
        'invoice_date',
        'grand_total',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'grand_total' => 'decimal:2',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'custom_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class, 'sales_invoice_id');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(InvoiceReturn::class, 'sales_invoice_id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'sales_invoice_id', 'id');
    }

    // Payment-related methods
    public function getTotalPayments(): float
    {
        return $this->paymentTransactions()
            ->where('type', 'cash_in')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getOutstandingAmount(): float
    {
        return $this->grand_total - $this->getTotalPayments();
    }

    public function isFullyPaid(): bool
    {
        return $this->getOutstandingAmount() <= 0;
    }

    public function isPartiallyPaid(): bool
    {
        $totalPayments = $this->getTotalPayments();
        return $totalPayments > 0 && $totalPayments < $this->grand_total;
    }

    public function isUnpaid(): bool
    {
        return $this->getTotalPayments() == 0;
    }

    public function getPaymentStatus(): string
    {
        if ($this->isFullyPaid()) {
            return 'Fully Paid';
        } elseif ($this->isPartiallyPaid()) {
            return 'Partially Paid';
        }
        return 'Unpaid';
    }

    public function getPaymentStatusColor(): string
    {
        if ($this->isFullyPaid()) {
            return 'success';
        } elseif ($this->isPartiallyPaid()) {
            return 'warning';
        }
        return 'danger';
    }

    // Return-related methods
    public function getTotalReturns(): float
    {
        return $this->returns()->sum('total_amount');
    }

    public function getTotalRefunds(): float
    {
        return $this->paymentTransactions()
            ->where('type', 'cash_out')
            ->where('status', 'completed')
            ->whereNotNull('invoice_return_id')
            ->sum('amount');
    }

    public function getAvailableForReturn(): float
    {
        $totalPaid = $this->getTotalPayments();
        $totalRefunds = $this->getTotalRefunds();
        return max(0, $totalPaid - $totalRefunds);
    }

    public function hasUnrefundedReturns(): bool
    {
        $totalReturns = $this->getTotalReturns();
        $totalRefunds = $this->getTotalRefunds();
        return $totalReturns > $totalRefunds;
    }

    public static function generateInvoiceNo(): string
    {
        $lastInvoice = self::latest('invoice_no')->first();
        $number = $lastInvoice ? ((int) substr($lastInvoice->invoice_no, 3)) + 1 : 1;
        return 'INV' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'hold' => 'warning',
            'finalized' => 'success',
            default => 'secondary'
        };
    }
} 