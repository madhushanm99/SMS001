<?php

namespace App\Models;

use App\Notifications\CustomerVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CustomerLogin extends Authenticatable implements MustVerifyEmail
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
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'reset_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'reset_token_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'must_change_password' => 'boolean',
    ];

    /**
     * Get the customer associated with the login.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_custom_id', 'custom_id');
    }

    /**
     * Send the email verification notification using the customer-specific route.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomerVerifyEmail());
    }
}
