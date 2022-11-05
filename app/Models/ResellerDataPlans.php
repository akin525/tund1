<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResellerDataPlans extends Model
{
    protected $table = "tbl_reseller_dataplans";
    protected $fillable = ["name", "code", "amount", "status", "type", "price", "level1", "level2", "level3", "level4", "level5", "product_code", "server"];
}
