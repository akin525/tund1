<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table='tbl_allsettings';

    protected $fillable = [
        'name', 'value'
    ];
}
