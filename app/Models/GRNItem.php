<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GRNItem extends Model
{
    protected $table = 'grn_items';
    protected $primaryKey = 'grn_item_id';

    protected $fillable = [
        'grn_id',
        'item_ID',
        'item_Name',
        'qty_received',
        'price',
        'line_total',
        'cost_value',
        'discount',
        'remarks'
    ];

    public function grn()
    {
        return $this->belongsTo(GRN::class, 'grn_id', 'grn_id');
    }
}
