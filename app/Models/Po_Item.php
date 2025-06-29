<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Po_Item extends Model
{
    protected $table = 'po_Items';

    // Primary key
    protected $primaryKey = 'po_Item_Auto_ID';

    // Indicates if the IDs are auto-incrementing
    public $incrementing = true;

    // The "type" of the auto-incrementing ID
    protected $keyType = 'int';

    // Timestamps
    public $timestamps = true;

    // The attributes that are mass assignable
    protected $fillable = [
        'po_No',
        'list_No',
        'item_ID',
        'qty',
        'price',
        'line_Total',
        'status',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(Po::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}
