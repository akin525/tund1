<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class PndL extends Model
{
    protected $table='tbl_p_nd_l';

    protected $fillable = [
    'type', 'amount', 'narration', 'date', 'gl'
    ];
}
