<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table='tbl_wallet';

    protected $fillable = [
        'o_wallet', 'n_wallet', 'medium', 'status', 'date', 'user_name', 'amount', 'ref', 'version', 'deviceid'
    ];
}
