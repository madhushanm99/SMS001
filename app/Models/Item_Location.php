<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item_Location extends Model
{

    protected $primaryKey = 'iD_Auto';

    protected $table = 'item_location';

    public function getRouteKeyName()
    {
        return 'iD_Auto';
    }
    protected $fillable = [
        'location_Name',
        'description',
        'created_at',
        'updated_at',
        'status',
    ];
}
