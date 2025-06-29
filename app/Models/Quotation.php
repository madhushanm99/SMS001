<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_no',
        'customer_custom_id',
        'vehicle_no',
        'quotation_date',
        'grand_total',
        'note',
        'created_by',
        'status'
    ];

    public static function generateQuotationNo(): string
    {
        $last = self::latest('id')->first();
        $number = $last ? (int) substr($last->quotation_no, 2) + 1 : 1;
        return 'QT' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
