<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airtime2Cash extends Model
{
    protected $table='tbl_airtime2cash';

    protected $fillable = [
        'network', 'amount', 'phoneno', 'receiver', 'user_name', 'ip', 'device_details', 'version', 'ref', 'webhook_url'
    ];
}
