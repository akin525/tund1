<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Nuban;
use App\Models\VirtualAccountClient;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OthersController extends Controller
{
    public function reserveAccount(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'account_name' => 'required|min:5',
            'business_short_name' => 'required|min:2',
            'email' => 'required|email|min:5',
            'phone' => 'required|min:5',
            'uniqueid' => 'required|min:9',
            'webhook_url' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $key = $request->header('Authorization');

        $user = User::where("api_key", $key)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact us on whatsapp@07011223737']);
        }

        $ui = VirtualAccountClient::where('account_reference', $input['uniqueid'])->first();

        if ($ui) {
            return response()->json(['success' => 0, 'message' => 'Kindly provide a unique ID']);
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('PAYSTACK_URL') . "customer",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"email": "' . $input['email'] . '", "first_name": "' . $input['account_name'] . '","last_name": "' . $input['business_short_name'] . '", "phone": "' . $input['phone'] . '"}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('PAYSTACK_SECRET_KEY'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);


        $resp = json_decode($response, true);


        if (!$resp['status']) {
            return response()->json(['success' => 0, 'message' => 'An error occurred. Kindly contact admin']);
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('PAYSTACK_URL') . "dedicated_account",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"customer": "' . $resp['data']['customer_code'] . '","preferred_bank": "wema-bank"}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('PAYSTACK_SECRET_KEY'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response, true);

        if ($res['status']) {

            VirtualAccountClient::create([
                "reseller_id" => $user->id,
                "account_reference" => $input['uniqueid'],
                "currency_code" => "NGN",
                "customer_email" => $input['email'],
                "customer_name" => $input['account_name'],
                "customer_phone" => $input['phone'],
                "account_number" => $res['data']['account_number'],
                "bank_name" => $res['data']['bank']['name'],
                "status" => "active",
                "created_on" => Carbon::now(),
                "reservation_reference" => $resp['data']['customer_code'],
                "webhook_url" => $input['webhook_url'],
                "extra" => $response
            ]);

            return response()->json(['success' => 1, 'message' => 'Virtual bank account created successfully', 'data' => [
                "account_reference" => $input['uniqueid'],
                "account_name" => $res['data']['account_name'],
                "account_number" => $res['data']['account_number'],
                "bank_name" => $res['data']['bank']['name']
            ]]);

        }

        return response()->json(['success' => 0, 'message' => 'An error occurred. Kindly contact admin']);

    }

    public function reserveAccount0(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'account_name' => 'required|min:5',
            'email' => 'required|email|min:5',
            'bvn' => 'required|min:11',
            'uniqueid' => 'required|min:9',
            'webhook_url' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $key = $request->header('Authorization');

        $user = User::where("api_key", $key)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact us on whatsapp@07011223737']);
        }

        $ui = VirtualAccountClient::where('account_reference', $input['uniqueid'])->first();

        if ($ui) {
            return response()->json(['success' => 0, 'message' => 'Kindly provide a unique ID']);
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.korapay.com/merchant/api/v1/virtual-bank-account',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => '{
    "account_name": "' . $input['account_name'] . '",
    "account_reference": "' . $input['uniqueid'] . '",
    "permanent": true,
    "bvn": ["' . $input['bvn'] . '"],
    "bank_code": "035",
    "customer": {
        "name": "' . $input['account_name'] . '"
    }
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('KORAPAY_SECRET_KEY'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        echo $response;

        $rep = json_decode($response, true);

        if ($rep['status']) {
            VirtualAccountClient::create([
                "reseller_id" => $user->id,
                "account_reference" => $input['uniqueid'],
                "currency_code" => $rep['data']['currency'],
                "customer_email" => $input['email'],
                "customer_name" => $rep['data']['account_name'],
                "account_number" => $rep['data']['account_number'],
                "bank_name" => $rep['data']['bank_name'],
                "status" => $rep['data']['account_status'],
                "created_on" => $rep['data']['created_at'],
                "reservation_reference" => $rep['data']['unique_id'],
                "webhook_url" => $input['webhook_url'],
                "extra" => $response
            ]);

            return response()->json(['success' => 1, 'message' => 'Virtual bank account created successfully', 'data' => [
                "account_reference" => $input['uniqueid'],
                "customer_name" => $rep['data']['account_name'],
                "account_number" => $rep['data']['account_number'],
                "bank_name" => $rep['data']['bank_name'],
                "bank_code" => $rep['data']['bank_code'],
            ]]);
        }

        return response()->json(['success' => 0, 'message' => 'An error occurred. Kindly contact admin']);

    }

    // Generate Dedicated NUBAN (Virtual Account Number)
    public function generateVAccount()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('PAYSTACK_URL') . "dedicated_account",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"customer": "' . auth()->user()->pk_customer_code . '","preferred_bank": "wema-bank"}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('PAYSTACK_SECRET_KEY'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response, true);

        if ($res['status'] == true) {
            $dn['user_id'] = auth()->user()->id;
            $dn['customer_id'] = auth()->user()->pk_customer_id;
            $dn['customer_code'] = auth()->user()->pk_customer_code;
            $dn['bank_name'] = $res['data']['bank']['name'];
            $dn['bank_slug'] = $res['data']['bank']['slug'];
            $dn['account_name'] = $res['data']['account_name'];
            $dn['account_number'] = $res['data']['account_number'];
            Nuban::create($dn);

            $pkc = User::findOrFail(auth()->user()->id);
            $pkc->acc_status = 1;
            $pkc->save();
            return back()->with("success", $res['message']);
        }

        return back()->with("error", "Unable to create your NUBAN Account Now. Try again later.");

    }

}
