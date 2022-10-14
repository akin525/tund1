<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CGTransaction extends Model
{
    protected $table="tbl_cg_transactions";

    protected $guarded=[];

    function cgbundle(){
        return $this->belongsTo(CGBundle::class, "bundle_id");
    }
}
