<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{
    use HasFactory;
    protected $primaryKey = 'item_ID_Auto';

    protected $table = 'item';

    // public function getRouteKeyName()
    // {
    //     return 'item_ID_Auto';
    // }

    protected $fillable = [
        'item_ID',
        'item_Name',
        'product_Type',
        'catagory_Name',
        'sales_Price',
        'units',
        'reorder_level',
        'unitofMeture',
        'location',
        'created_at',
        'updated_at',
        'status',
    ];


    public static function updatePriceIfHigher($itemId, $newPrice)
    {
        $item = self::find($itemId);
        if ($item && $newPrice > $item->sales_Price) {
            $item->update(['sales_Price' => $newPrice]);
        }
    }
}
