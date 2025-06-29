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
        'emp_Name',
        'status',
    ];
}
