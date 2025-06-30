<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceReturn extends Model
{
    protected $fillable = [
        'return_no',
        'sales_invoice_id',
        'invoice_no',
        'customer_id',
        'return_date',
        'total_amount',
        'reason',
        'notes',
        'processed_by',
        'status',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'custom_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceReturnItem::class, 'invoice_return_id');
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'invoice_return_id');
    }

    // Payment-related methods
    public function getTotalRefunds(): float
    {
        return $this->paymentTransactions()
            ->where('type', 'cash_out')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function isRefundProcessed(): bool
    {
        return $this->getTotalRefunds() > 0;
    }

    public function getRefundStatus(): string
    {
        $totalRefunds = $this->getTotalRefunds();
        
        if ($totalRefunds >= $this->total_amount) {
            return 'fully_refunded';
        } elseif ($totalRefunds > 0) {
            return 'partially_refunded';
        } else {
            return 'not_refunded';
        }
    }

    public function getRefundStatusColor(): string
    {
        return match($this->getRefundStatus()) {
            'fully_refunded' => 'success',
            'partially_refunded' => 'warning',
            'not_refunded' => 'danger',
            default => 'secondary'
        };
    }

    public static function generateReturnNo(): string
    {
        $lastReturn = self::latest('return_no')->first();
        $number = $lastReturn ? ((int) substr($lastReturn->return_no, 3)) + 1 : 1;
        return 'RTN' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'completed' => 'success',
            default => 'secondary'
        };
    }
} 