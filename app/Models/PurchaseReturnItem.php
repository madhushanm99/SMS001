<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable = [
        'purchase_return_id',
        'item_ID',
        'item_Name',
        'qty_returned',
        'price',
        'line_total',
        'reason'
    ];
    public function return()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id');
    }
}
