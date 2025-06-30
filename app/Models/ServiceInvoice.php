<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceInvoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'customer_id',
        'vehicle_no',
        'mileage',
        'invoice_date',
        'job_total',
        'parts_total',
        'grand_total',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'job_total' => 'decimal:2',
        'parts_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'mileage' => 'integer',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'custom_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_no', 'vehicle_no');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceInvoiceItem::class, 'service_invoice_id');
    }

    public function jobItems(): HasMany
    {
        return $this->hasMany(ServiceInvoiceItem::class, 'service_invoice_id')->where('item_type', 'job');
    }

    public function spareItems(): HasMany
    {
        return $this->hasMany(ServiceInvoiceItem::class, 'service_invoice_id')->where('item_type', 'spare');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'service_invoice_id', 'id');
    }

    // Generate invoice number
    public static function generateInvoiceNo(): string
    {
        $lastInvoice = self::latest('id')->first();
        $number = $lastInvoice ? (int) substr($lastInvoice->invoice_no, 3) + 1 : 1;
        return 'SRV' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    // Calculate totals
    public function calculateTotals(): void
    {
        $this->job_total = $this->jobItems()->sum('line_total');
        $this->parts_total = $this->spareItems()->sum('line_total');
        $this->grand_total = $this->job_total + $this->parts_total;
        $this->save();
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
            return 'fully_paid';
        } elseif ($this->isPartiallyPaid()) {
            return 'partially_paid';
        } else {
            return 'unpaid';
        }
    }

    public function canBeFinalized(): bool
    {
        return $this->status === 'hold' && $this->items()->count() > 0;
    }

    public function finalize(): bool
    {
        if (!$this->canBeFinalized()) {
            return false;
        }

        $this->status = 'finalized';
        $this->calculateTotals();
        
        return true;
    }
} 