<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobTypes extends Model
{
    protected $table = 'job_types';

    protected $fillable = [
        'jobCustomID',
        'jobType',
        'salesPrice',
        'status',
    ];


    protected $casts = [
        'salesPrice' => 'decimal:2',
        'status' => 'boolean',
    ];


    public static function generateJobCustomID(): string
    {
        $lastJobType = self::latest('id')->first();
        $number = $lastJobType ? (int) substr($lastJobType->jobCustomID, 3) + 1 : 1;
        return 'JOB' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
