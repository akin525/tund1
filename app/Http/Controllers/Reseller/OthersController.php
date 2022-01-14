<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\VirtualAccountClient;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OthersController extends Controller
{
    public function reserveAccount(Request $request)
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
}
