<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\model\Transaction;
use App\User;
use Illuminate\Http\Request;
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
                $trans=Transaction::OrderBy('id', 'desc')->limit(50)->get();
            }else{
                $trans=Transaction::where('user_name',$input["user_name"])->OrderBy('id', 'desc')->limit(50)->get();

                if ($trans->isEmpty()){
                    return response()->json(['success' => 1, 'message' => 'No transactions found']);
                }
            }
            return response()->json(['success' => 1, 'message' => 'Transactions Fetched', 'data'=>$trans, 'wallet'=>$user->wallet]);
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }


}
