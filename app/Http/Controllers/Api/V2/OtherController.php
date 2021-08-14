<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Settings;

class OtherController extends Controller
{
    public function paymentcheckout()
    {
        $settings = Settings::all();
        foreach ($settings as $setting) {
            $sett[$setting->name] = $setting->value;
        }

        $data['rave'] = $sett['fund_rave'];
        $data['paystack'] = $sett['fund_paystack'];
        $data['payant'] = $sett['fund_payant'];
        $data['bank'] = $sett['fund_bank'];
        $data['monnify'] = $sett['fund_monnify'];
        $data['korapay'] = $sett['fund_korapay'];
        $data['wallet'] = $sett['fund_bank'];

        $d['paystack_public'] = $sett['fund_paystack_details'];
        $d['paystack_secret'] = $sett['secret_paystack_details'];
        $d['rave_public'] = $sett['fund_rave_details'];
        $d['rave_enckey'] = $sett['fund_rave_key'];
        $d['monnify_apikey'] = $sett['fund_monnify_apikey'];
        $d['monnify_contractcode'] = $sett['fund_monnify_contractcode'];

        return response()->json(['success' => 1, 'message' => 'Fetched successful', 'data' => ['status' => $data, 'details' => $d]]);
    }
}
