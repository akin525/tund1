<?php

namespace App\Http\Controllers;

use App\Models\PndL;
use App\Models\Transaction;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){

        $wallet = DB::table('tbl_wallet')->orderBy('id', 'desc')->paginate(25);

        return view('wallets', ['data' => $wallet]);

    }

    public function addfund(Request $request){
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'payment_channel'      => 'required',
            'amount' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes())
        {
            $sms_id="15658";
            $sms_secret="66Wby95tGM15Wo3uQk1OwiYO3muum4Ds";
            $sms_pass="zEKJKdpxfvuDzYtTZipihelDJQ0NttZ28JMSXbpcHT";
            $sms_senderid="MCD Wallet";
            $sms_charges=3;
            $charge_treshold=2000;
            $charges=50;
            $amount=$input["amount"];
            $user= User::where('user_name', $input["user_name"])->first();
            $sms_description="Dear ".$input['user_name'].", your MCD wallet has been credited with ".$amount." via ".$input['payment_channel'].". Thanks for choosing Mega Cheap Data.";

            if($user){
//                oladiplenty100 wallet funded using Payant with the sum of #600
                $input["description"]=$input["user_name"] . " wallet funded using ".$input["payment_channel"]. " with the sum of #".$input["amount"]. $input["odescription"];
                $input["i_wallet"]=$user->wallet;
                $input["f_wallet"]=$input["i_wallet"] + $input["amount"];
                $input["ip_address"]="127.0.0.1";
                $input["code"]="fund_".$input["payment_channel"];
                $input["status"]="successful";
                $input["date"]=date("y-m-d H:i:s");
                $input["name"]="wallet funding";
                $input["extra"]='Initiated by ' . Auth::user()->full_name;

                Transaction::create($input);

                if($input["amount"]<$charge_treshold){
                    $input["type"]="income";
                    $input["amount"]=$charges;
                    $input["narration"]="Being amount charged for funding less than #".$charge_treshold." from ".$input["user_name"];

                    PndL::create($input);

                    $input["description"]="Being amount charged for funding less than #2,000";
                    $input["name"]="Auto Charge";
                    $input["code"]="ac50";
                    $input["i_wallet"]=$input["f_wallet"];
                    $input["f_wallet"]=$input["f_wallet"] - $charges;

                    Transaction::create($input);


                    $amount-=$charges;
                }

//                $input["description"]="Being sms charge";
//                $input["name"]="SMS Charge";
//                $input["amount"]=$sms_charges;
//                $input["code"]="smsc";
//                $input["i_wallet"]=$input["f_wallet"];
//                $input["f_wallet"]=$input["f_wallet"] - $sms_charges;
//
//                Transaction::create($input);
//
//                $amount-=$sms_charges;

                $user->wallet+=$amount;
                $user->save();

//                $curl = curl_init();
//                curl_setopt_array($curl, array(
//                    CURLOPT_URL => "http://www.sms.5starcompany.com.ng/smsapi?pd_m=send&id=".$sms_id."&secret=".$sms_secret."&pass=".$sms_pass."&senderID=".$sms_senderid."&to_number=".$user->phoneno."&textmessage=".$sms_description,
//                    CURLOPT_RETURNTRANSFER => true,
//                    CURLOPT_CUSTOMREQUEST => "GET",
//                    CURLOPT_HTTPHEADER => [
//                        "content-type: application/json",
//                        "cache-control: no-cache"
//                    ],
//                ));
//
//                $response = curl_exec($curl);
//                $err = curl_error($curl);
//
//                if($err){
//                    // there was an error contacting the SMS Portal
//                    die('Curl returned error: ' . $err);
//                }
//
//                DB::table('tbl_smslog')->insert(
//                    ['user_name' => $input["user_name"], 'message' => $sms_description, 'phoneno' => $user->phoneno, 'response' => $response]
//                );


                $at = new PushNotificationController();
                $at->PushNoti($input['user_name'], $input["description"], "Wallet Funded");

                return redirect('/addfund')->with('success', $input["user_name"]. ' wallet funded successfully!');
            }else{
                        $validator->errors()->add('username', 'The username does not exist!');

                return redirect('/addfund')
                    ->withErrors($validator)
                    ->withInput($input);
            }

        }else{

            return redirect('/addfund')
                ->withErrors($validator)
                ->withInput($input);
//            return response()->json(['status'=> 0, 'message'=>'Unable to login with errors', 'error' => $validator->errors()]);;
        }
    }
}
