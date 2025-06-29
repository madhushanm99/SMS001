<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item_category extends Model
{
    protected $primaryKey = 'iD_Auto';
    protected $fillable = [
        'category_Name',
        'description',
        'created_at',
        'updated_at',
        'status',
    ];
}
