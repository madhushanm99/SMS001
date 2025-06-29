<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'Supp_ID';

    public function getRouteKeyName()
    {
        return 'Supp_ID';
    }

    protected $fillable = [
        'Supp_ID',
        'Supp_CustomID',
        'Supp_Name',
        'Company_Name',
        'Phone',
        'Fax',
        'Email',
        'Web',
        'Address1',
        'Supp_Group_Name',
        'Remark',
        'Last_GRN',
        'Total_Orders',
        'Total_Spent',
    ];
}
