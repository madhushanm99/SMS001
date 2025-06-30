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
        'cost_value',
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
                'cost_value' => 0, // Will be updated by updateCostIfHigher method
            ]);
        }
    }

    public static function updateCostIfHigher($itemId, $grnUnitCost)
    {
        $stock = self::where('item_ID', $itemId)->first();
        
        if ($stock) {
            // Update cost only if GRN cost is higher than current cost
            if ($grnUnitCost > $stock->cost_value) {
                $stock->update(['cost_value' => $grnUnitCost]);
            }
        } else {
            // Create new stock record with the GRN cost
            self::create([
                'item_ID' => $itemId,
                'quantity' => 0,
                'cost_value' => $grnUnitCost,
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

    public static function decrease($itemId, $quantity)
    {
        return self::reduce($itemId, $quantity);
    }

}
