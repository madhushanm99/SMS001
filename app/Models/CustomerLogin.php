<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CustomerLogin extends Authenticatable
{
    use Notifiable;

    protected $table = 'customer_logins';

    protected $fillable = [
        'customer_custom_id',
        'email',
        'password',
        'reset_token',
        'reset_token_expires_at',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'reset_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'reset_token_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the customer associated with the login.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_custom_id', 'custom_id');
    }
}
