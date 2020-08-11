<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GeneralMarket extends Model
{
    protected $table='tbl_generalmarket';

    protected $fillable = [
        'o_wallet', 'n_wallet', 'type', 'date', 'user_name', 'amount', 'transid', 'version', 'device_details'
    ];
}
