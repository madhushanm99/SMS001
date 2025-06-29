<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'custom_id');
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'sales_invoice_id');
    }

    public function returns()
    {
        return $this->hasMany(InvoiceReturn::class, 'sales_invoice_id');
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