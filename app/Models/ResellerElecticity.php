<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResellerElecticity extends Model
{
    protected $table = "tbl_reseller_electricity";
    protected $fillable = ['name', 'code', 'discount', 'status'];

}
