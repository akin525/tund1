<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppCableTVControl extends Model
{
    protected $table = "tbl_serverconfig_cabletv";
    protected $fillable = ["name", "coded", "code", "price", "discount", "status", "type", "server"];
}
