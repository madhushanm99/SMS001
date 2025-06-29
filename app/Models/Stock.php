<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Products;

class Stock extends Model
{
    protected $table = 'stock';

    protected $fillable = ['item_ID', 'quantity', 'location'];

    public function item()
    {
        return $this->belongsTo(Products::class, 'item_ID', 'item_ID');
    }

    public static function increase($itemId, $qty)
    {
        $stock = self::firstOrCreate(['item_ID' => $itemId]);
        $stock->increment('quantity', $qty);
    }

    public static function decrease($itemId, $qty)
    {
        $stock = self::where('item_ID', $itemId)->first();
        if ($stock) {
            $stock->decrement('quantity', $qty);
        }
    }

}
