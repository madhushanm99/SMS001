<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationItem extends Model
{
    use SoftDeletes;
    
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

    protected $dates = ['deleted_at'];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'status' => 'boolean',
    ];
}
