<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'usertype',
        'suppliers',
        'products',
        'purchaseOrder',
        'recevingGRN',
        'purchaseReturn',
        // Add other actions here
    ];
}
