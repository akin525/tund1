<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResellerCableTV extends Model
{
    protected $table = "tbl_reseller_cabletv";
    protected $fillable = ["name", "code", "amount", "discount", "status", "type"];
}
