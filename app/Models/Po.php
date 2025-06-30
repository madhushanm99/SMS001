<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Po extends Model
{
    // Table name (if not the plural of the model name)
    protected $table = 'po';

    // Primary key
    protected $primaryKey = 'po_Auto_ID';

    // Indicates if the IDs are auto-incrementing
    public $incrementing = true;

    // The "type" of the auto-incrementing ID
    protected $keyType = 'int';

    // Timestamps
    public $timestamps = true;

    // The attributes that are mass assignable
    protected $fillable = [
        'po_No',
        'po_date',
        'supp_Cus_ID',
        'grand_Total',
        'note',
        'Reff_No',
        'orderStatus',
        'emp_Name',
        'status',
    ];

    protected $casts = [
        'po_date' => 'date',
        'grand_Total' => 'decimal:2'
    ];

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supp_Cus_ID', 'Supp_CustomID');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Po_Item::class, 'po_Auto_ID', 'po_Auto_ID');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'purchase_order_id', 'po_Auto_ID');
    }

    // Payment-related methods
    public function getTotalPayments(): float
    {
        return $this->paymentTransactions()
            ->where('type', 'cash_out')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getOutstandingAmount(): float
    {
        return $this->grand_Total - $this->getTotalPayments();
    }

    public function isFullyPaid(): bool
    {
        return $this->getOutstandingAmount() <= 0;
    }

    public function isPartiallyPaid(): bool
    {
        $totalPayments = $this->getTotalPayments();
        return $totalPayments > 0 && $totalPayments < $this->grand_Total;
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

    public function calculateTotal()
    {
        return $this->items()->sum('line_total');
    }

    public static function generatePONumber()
    {
        $lastPO = self::latest('po_No')->first();
        $number = $lastPO ? (int) substr($lastPO->po_No, 2) + 1 : 1;
        return 'PO' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
