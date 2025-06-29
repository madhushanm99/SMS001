<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceReturnItem extends Model
{
    protected $fillable = [
        'invoice_return_id',
        'line_no',
        'item_id',
        'item_name',
        'qty_returned',
        'original_qty',
        'unit_price',
        'discount',
        'line_total',
        'return_reason',
    ];

    protected $casts = [
        'qty_returned' => 'integer',
        'original_qty' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function invoiceReturn()
    {
        return $this->belongsTo(InvoiceReturn::class, 'invoice_return_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'item_id', 'item_ID');
    }
} 