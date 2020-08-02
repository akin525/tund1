<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table='tbl_transactions';

    protected $fillable = [
        'name', 'amount', 'status', 'description', 'date', 'user_name', 'ip_address', 'device_details', 'code', 'i_wallet', 'f_wallet', 'extra', 'server', 'server_response'
    ];

}
