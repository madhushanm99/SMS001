<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Products;

class Stock extends Model
{
    protected $table = 'stock';

    protected $fillable = [
        'item_ID',
        'quantity',
        'updated_at',
    ];

    public function item()
    {
        return $this->belongsTo(Products::class, 'item_ID', 'item_ID');
    }

    public static function increase($itemId, $quantity)
    {
        $stock = self::where('item_ID', $itemId)->first();
        
        if ($stock) {
            $stock->increment('quantity', $quantity);
        } else {
            self::create([
                'item_ID' => $itemId,
                'quantity' => $quantity,
            ]);
        }
    }

    public static function reduce($itemId, $quantity)
    {
        $stock = self::where('item_ID', $itemId)->first();
        
        if ($stock && $stock->quantity >= $quantity) {
            $stock->decrement('quantity', $quantity);
            return true;
        }
        
        return false;
    }

}
