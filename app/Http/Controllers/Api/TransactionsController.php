<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Model\GeneralMarket;
use App\Model\Settings;
use App\Model\Transaction;
use App\Model\Wallet;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    public function getTrans(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'version'      => 'required',
            'deviceid'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {

            $user = User::where('user_name', $input["user_name"])->first();

            if (!$user) {
                return response()->json(['success' => 0, 'message' => 'User not found']);
            }

            if($user->status=="admin" || $user->status=="staff"){
                $trans=Transaction::OrderBy('id', 'desc')->limit(100)->get();
            }else{
                $trans=Transaction::where('user_name',$input["user_name"])->OrderBy('id', 'desc')->limit(50)->get();

                if ($trans->isEmpty()){
                    return response()->json(['success' => 1, 'message' => 'No transactions found', 'wallet'=>$user->wallet]);
                }
            }
            return response()->json(['success' => 1, 'message' => 'Transactions Fetched', 'data'=>$trans, 'wallet'=>$user->wallet]);
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function getGmTrans(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'version'      => 'required',
            'deviceid'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {

            $user = User::where('user_name', $input["user_name"])->first();

            $set=Settings::where('name','general_market')->first();

            if (!$user) {
                return response()->json(['success' => 0, 'message' => 'User not found']);
            }

                $trans=GeneralMarket::OrderBy('id', 'desc')->limit(300)->get();
                if ($trans->isEmpty()){
                    return response()->json(['success' => 1, 'message' => 'No transactions found', 'wallet'=>$set->value]);
                }
            return response()->json(['success' => 1, 'message' => 'General Market Transactions Fetched', 'data'=>$trans, 'wallet'=>$set->value]);
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function getPortalTrans(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'version'      => 'required',
            'deviceid'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {

            $user = User::where('user_name', $input["user_name"])->first();

            if (!$user) {
                return response()->json(['success' => 0, 'message' => 'User not found']);
            }

            if($user->status=="admin" || $user->status=="staff"){
                $trans=Transaction::where('status', 'LIKE', '%API%')->OrderBy('id', 'desc')->limit(100)->get();
                $count=Transaction::where('status', 'LIKE', '%API%')->OrderBy('id', 'desc')->count();
            }else{
                $trans=Transaction::where([['user_name',$input["user_name"]], ['status', 'LIKE', '%API%']])->OrderBy('id', 'desc')->limit(100)->get();
                $count=Transaction::where([['user_name',$input["user_name"]], ['status', 'LIKE', '%API%']])->OrderBy('id', 'desc')->count();

                if ($trans->isEmpty()){
                    return response()->json(['success' => 1, 'message' => 'No transactions found', 'count'=>$count]);
                }
            }
            return response()->json(['success' => 1, 'message' => 'Transactions Fetched', 'data'=>$trans, 'count'=>$count]);
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function fundWallet(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'amount'      => 'required',
            'medium'      => 'required',
            'o_wallet'      => 'required',
            'n_wallet'      => 'required',
            'ref'      => 'required',
            'version'      => 'required',
            'deviceid'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {

            $user = User::where('user_name', $input["user_name"])->first();

            if (!$user) {
                return response()->json(['success' => 0, 'message' => 'User not found']);
            }

            $input['date']=Carbon::now();

            Wallet::create($input);

            return response()->json(['success' => 1, 'message' => 'Fund Logged for further processing']);
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function insertRechargecard(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'net'      => 'required',
            'qty'      => 'required',
            'spec'      => 'required',
            'price'      => 'required',
            'price2'      => 'required',
            'ref'      => 'required',
            'version'      => 'required',
            'device_details'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {

            $user = User::where('user_name', $input["user_name"])->first();

            if (!$user) {
                return response()->json(['success' => 0, 'message' => 'User not found']);
            }

            $uid = $input['user_name'];
            $net = $input['net'];
            $qty = $input['qty'];
            $spec=$input["spec"];
            $price = $input['price'];
            $price2=$input["price2"];
            $p=$price*$price2*$qty;
            $ref=$input["ref"];

            if($p > $user->wallet){
                return response()->json(['success'=> 0, 'message'=>'Insufficient Balance']);
            }

                $input["i_wallet"]=$user->wallet;
                $GLOBALS['email']=$user->email;
                $input['f_wallet']=$input["i_wallet"]-$p;
                $input['amount']=$p;

                $input['description']=$uid." order ".$net."(#". $spec . ") recharge card of ".$qty." quantity with ref ".$ref;
                $input['extra']="qty-".$qty.", net-".$net.", spec-".$spec.", ref-".$ref;
                $input['ip_address']=$_SERVER['REMOTE_ADDR'];
                $input['date']=Carbon::now();
                $input['name']='Recharge Card';
                $input['status']='submitted';
                $input['code']='rcc';

                Transaction::create($input);

                $user->wallet=$input['f_wallet'];
                $user->save();

                $data = array('name' => $user->user_name, 'date' => date("D, d M Y"));
                Mail::send('email_rechargecard_notice', $data, function ($message) {
                    $message->to($GLOBALS['email'], 'MCD Customer')->subject('MCD Rechargecard');
                    $message->from('info@5starcompany.com.ng', '5Star Inn Company');
                });

                return response()->json(['success' => 1, 'message' => 'Transactions Added Successfully']);

        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function insertResultchecker(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'net'      => 'required',
            'qty'      => 'required',
            'spec'      => 'required',
            'price'      => 'required',
            'ref'      => 'required',
            'version'      => 'required',
            'device_details'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {

            $user = User::where('user_name', $input["user_name"])->first();

            if (!$user) {
                return response()->json(['success' => 0, 'message' => 'User not found']);
            }
            if($input["price"] > $user->wallet){
                return response()->json(['success'=> 0, 'message'=>'Insufficient Balance']);
            }

            $uid = $input['user_name'];
            $net = $input['net'];
            $qty = $input['qty'];
            $spec=$input["spec"];
            $price = $input['price'];
            $p=$price*$qty;
            $ref=$input["ref"];
            $input["i_wallet"]=$user->wallet;
            $email=$input["email"];
            $input['f_wallet']=$input["i_wallet"]-$p;
            $input['amount']=$p;

            $input['date']=Carbon::now();
            $input['ip_address']=$_SERVER['REMOTE_ADDR'];
            $input['description']=$uid." order ".$net." result checker of ".$qty." quantity with ref ".$ref;
            $input['extra']="qty-".$qty.", net-".$net.", spec-".$spec.", ref-".$ref;
            $input['name']='Result Checker';
            $input['status']='submitted';
            $input['code']='rch';

            // mysql inserting a new row
            Transaction::create($input);

            $user->wallet=$input['f_wallet'];
            $user->save();

            $at=new PushNotificationController();
            $at->PushNoti($input['user_name'], "Hi ".$input['user_name'].", you will receive your ". $net." request in your mail soon. Thanks", "Result Checker");

            return response()->json(['success' => 1, 'message' => 'Transactions Added Successfully']);
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function insertFreemoney(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'code'      => 'required',
            'i_wallet'      => 'required',
            'version'      => 'required',
            'device_details'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {

            $user = User::where('user_name', $input["user_name"])->first();

            if (!$user) {
                return response()->json(['success' => 0, 'message' => 'User not found']);
            }

            $input['name'] = "free_money";
            $input['status'] = "rewarded";
            $input['amount']=2;
            $input['description'] = "being payment of free money given to ".$input['user_name'];
            $input['i_wallet'] = $user->wallet;
            $input['f_wallet'] = $input['i_wallet']+$input['amount'];

            $input['date']=Carbon::now();
            $input['ip_address']=$_SERVER['REMOTE_ADDR'];

            // mysql inserting a new row
            Transaction::create($input);

            $user->wallet=$input['f_wallet'];
            $user->save();

            return response()->json(['success' => 1, 'message' => 'Transactions Added Successfully']);
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function getReferrals(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'version'      => 'required',
            'deviceid'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {

            $user = User::where('user_name', $input["user_name"])->first();

            if (!$user) {
                return response()->json(['success' => 0, 'message' => 'User not found']);
            }

            // get referrals out
            $referrals=User::where('referral', $input["user_name"])->select('user_name', 'status', 'reg_date', 'referral_plan')->get();
            $referrals_count=User::where('referral', $input["user_name"])->count();

            if($referrals_count == 0){
                return response()->json(['success'=> 0, 'message'=>'No referral found']);
            }

            return response()->json(['success' => 1, 'message' => 'Referrals Fetched Successfully', 'total_count'=>$referrals_count, 'data'=>$referrals]);
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }


}
