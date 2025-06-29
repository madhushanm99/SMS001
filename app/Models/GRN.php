<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GRN extends Model
{
    protected $table = 'grn';
    protected $primaryKey = 'grn_id';

    protected $fillable = [
        'grn_no',
        'grn_date',
        'po_Auto_ID',
        'po_No',
        'supp_Cus_ID',
        'invoice_no',
        'invoice_date',
        'received_by',
        'note',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(GRNItem::class, 'grn_id', 'grn_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supp_Cus_ID', 'Supp_CustomID');
    }

    public static function generateGRNNumber(): string
    {
        $lastGRN = self::latest('grn_no')->first();
        $number = $lastGRN ? (int) substr($lastGRN->grn_no, 3) + 1 : 1;
        return 'GRN' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

}

