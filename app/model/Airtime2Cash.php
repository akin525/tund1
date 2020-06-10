<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Airtime2Cash extends Model
{
    protected $table='tbl_voicelog';

    protected $fillable = [
        'network', 'amount', 'phoneno', 'receiver', 'user_name', 'ip', 'device_details', 'version', 'ref'
    ];
}
