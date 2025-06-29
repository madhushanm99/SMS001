<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Po extends Model
{
    // Table name (if not the plural of the model name)
    protected $table = 'po';

    // Primary key
    protected $primaryKey = 'po_Auto_ID';

    // Indicates if the IDs are auto-incrementing
    public $incrementing = true;

    // The "type" of the auto-incrementing ID
    protected $keyType = 'int';

    // Timestamps
    public $timestamps = true;

    // The attributes that are mass assignable
    protected $fillable = [
        'po_No',
        'po_date',
        'supp_Cus_ID',
        'grand_Total',
        'note',
        'Reff_No',
        'orderStatus',
        'emp_Name',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(Po_Item::class);
    }

    public function calculateTotal()
    {
        return $this->items()->sum('line_total');
    }

    public static function generatePONumber()
    {
        $lastPO = self::latest('po_No')->first();
        $number = $lastPO ? (int) substr($lastPO->po_No, 2) + 1 : 1;
        return 'PO' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
