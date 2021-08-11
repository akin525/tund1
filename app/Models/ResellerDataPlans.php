<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResellerDataPlans extends Model
{
    protected $table = "tbl_reseller_dataplans";
    protected $fillable = ["name", "code", "amount", "discount", "status", "type", "price"];
}
