<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'return_no',
        'grn_id',
        'grn_no',
        'supp_Cus_ID',
        'note',
        'returned_by',
        'status'
    ];
    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_return_id');
    }

    public static function generateReturnNo(): string
    {
        $last = self::latest()->first();
        $number = $last ? ((int) substr($last->return_no, 2)) + 1 : 1;
        return 'PR' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
