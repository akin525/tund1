<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SystemSettings extends Model
{
    protected $table='tbl_settings';

    protected $fillable = [
        'name'
    ];
}
