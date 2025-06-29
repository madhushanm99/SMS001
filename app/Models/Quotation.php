<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;
    
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

    protected $dates = ['deleted_at'];

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

    public function itemsWithTrashed()
    {
        return $this->hasMany(QuotationItem::class)->withTrashed();
    }
}
