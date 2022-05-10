<?php

namespace App\Http\Controllers\Reseller;

use App\user;
use Illuminate\Http\Request;

class BlockReseller
{
    public function listreseller(Request $request)
    {
        $reseller = user::where('status', 'client')->get();

        return view('seller', compact('reseller'));


    }

}
