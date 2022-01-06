<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\AirtimeCountry;
use App\Models\AppAirtimeControl;
use App\Models\AppCableTVControl;
use App\Models\AppDataControl;

class ListController extends Controller
{
    public function airtime()
    {
        //get airtime discounts
        $airsets = AppAirtimeControl::where('name', '=', 'discount')->first();

        $uinfo['discount_mtn'] = $airsets->mtn;
        $uinfo['discount_glo'] = $airsets->glo;
        $uinfo['discount_etisalat'] = $airsets->etisalat;
        $uinfo['discount_airtel'] = $airsets->airtel;

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $uinfo]);
    }

    public function airtimeInt()
    {
        $airsets = AirtimeCountry::get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $airsets]);
    }

    public function data($network)
    {

        $datasets = AppDataControl::where([['network', '=', strtoupper($network)], ['status', 1]])->select('name', 'coded', 'pricing as price', 'network', 'status')->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $datasets]);
    }

    public function cabletv($network)
    {

        $datasets = AppCableTVControl::where([['type', '=', strtolower($network)], ['status', 1]])->select('name', 'coded', 'price', 'type', 'status')->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $datasets]);
    }
}
