<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReminderLog extends Model
{
    protected $fillable = [
        'vehicle_no',
        'customer_custom_id',
        'customer_email',
        'next_service_date',
        'week_start',
        'week_end',
        'attempt',
        'status',
        'email_sent_at',
        'error_message',
    ];

    protected $casts = [
        'next_service_date' => 'date',
        'week_start' => 'date',
        'week_end' => 'date',
        'attempt' => 'integer',
        'email_sent_at' => 'datetime',
    ];
}


