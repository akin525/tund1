<?php

namespace App\Http\Controllers;

use App\model\PndL;
use App\model\Transaction;
use App\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index(Request $request){

        $wallet = DB::table('tbl_wallet')->orderBy('id', 'desc')->limit(500)->get();

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
            $charge_treshold=2000;
            $charges=50;
            $amount=$input["amount"];
            $user= User::where('user_name', $input["user_name"])->first();
            echo "user---" . $user;
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

                Transaction::create($input);

                if($input["amount"]<$charge_treshold){
                    $input["type"]="income";
                    $input["amount"]=$charges;
                    $input["narration"]="Being amount charged for funding less than #".$charge_treshold." from ".$input["user_name"];

                    PndL::create($input);

                    $input["narration"]="Being amount charged for funding less than #2,000";
                    $input["name"]="Auto Charge";
                    $input["code"]="ac50";
                    $input["i_wallet"]=$input["f_wallet"];
                    $input["f_wallet"]=$input["f_wallet"] - $charges;

                    Transaction::create($input);
                }
                $user->wallet+=$amount;
                $user->save();
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
