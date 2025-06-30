<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceInvoiceItem extends Model
{
    protected $fillable = [
        'service_invoice_id',
        'line_no',
        'item_type',
        'item_id',
        'item_name',
        'qty',
        'unit_price',
        'discount',
        'line_total',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function serviceInvoice(): BelongsTo
    {
        return $this->belongsTo(ServiceInvoice::class, 'service_invoice_id');
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobTypes::class, 'item_id', 'jobCustomID');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'item_id', 'item_ID');
    }

    // Calculate line total
    public function calculateLineTotal(): void
    {
        $this->line_total = ($this->qty * $this->unit_price) - $this->discount;
        $this->save();
    }

    // Get related item details based on type
    public function getItemDetails()
    {
        if ($this->item_type === 'job') {
            return $this->jobType;
        } elseif ($this->item_type === 'spare') {
            return $this->product;
        }
        return null;
    }
} 