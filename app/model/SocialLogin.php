<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SocialLogin extends Model
{
    protected $table='tbl_sociallogin';

    protected $fillable = [
        'name', 'avatar','email', 'accesstoken'
    ];
}
