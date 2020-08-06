<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Serverlog extends Model
{
    protected $table='tbl_severlog';

    protected $fillable = [
        'service', 'amount', 'phone', 'user_name', 'coded', 'wallet', 'api', 'date', 'ip_address', 'device_details', 'version', 'transid', 'status', 'payment_method'
    ];
}
