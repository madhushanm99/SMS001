<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'line_no',
        'item_type',
        'item_id',
        'description',
        'qty',
        'price',
        'line_total',
        'status'
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'status' => 'boolean',
    ];
}
