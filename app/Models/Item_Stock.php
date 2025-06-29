<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item_Stock extends Model
{

    protected $primaryKey = 'iD_Auto';

    protected $fillable = [
        'item_ID',
        'sales_Price',
        'qty',
        'reorder_Lvl',
        'reorder_Qty',
        'updated_at',
        'status',
    ];
}
