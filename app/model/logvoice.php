<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class logvoice extends Model
{
    protected $table='tbl_voicelog';

    protected $fillable = [
        'name', 'username', 'voice', 'version', 'page', 'code', 'device_details'
    ];
}
