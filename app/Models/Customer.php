<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
    'custom_id',
    'name',
    'phone',
    'email',
    'nic',
    'group_name',
    'address',
    'balance_credit',
    'last_visit',
    'user_id',
    'status',
];

public static function generateCustomID(): string
{
    $last = self::latest('id')->first();
    $next = $last ? ((int)substr($last->custom_id, 4)) + 1 : 1;
    return 'CUST' . str_pad($next, 6, '0', STR_PAD_LEFT);
}

public function login()
{
    //return $this->belongsTo(CustomerLogin::class, 'user_id');
}
}
