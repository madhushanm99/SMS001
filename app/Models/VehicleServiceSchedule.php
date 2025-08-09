<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleServiceSchedule extends Model
{
    protected $fillable = [
        'vehicle_no',
        'last_service_invoice_id',
        'last_service_date',
        'last_mileage',
        'next_service_date',
        'next_service_mileage',
        'days_until_next',
        'last_service_type',
        'calculated_at',
    ];

    protected $casts = [
        'last_service_date' => 'date',
        'next_service_date' => 'date',
        'last_mileage' => 'integer',
        'next_service_mileage' => 'integer',
        'days_until_next' => 'integer',
        'calculated_at' => 'datetime',
    ];
}


