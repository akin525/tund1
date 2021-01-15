<?php

namespace App\Http\Middleware;

use App\Model\Luno;
use App\Model\Serverlog;
use App\Model\Settings;
use App\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;

class ServerlogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $input=$request->all();
        $input['ip_address']=$_SERVER['REMOTE_ADDR'];

        if ($input['api'] != "mcd_app_9876234875356148750") {
            $input['status']='Unauthorized Access';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'Error, invalid request']);
        }

        $users=User::where("user_name","=",$input['user_name'])->first();
        if (!$users) {
            $input['status']='Username does not exist';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'Error, invalid user name']);
        }

        $re=Serverlog::where('transid',$input['transid'])->first();

        if($re){
            if($input['payment_method'] =="btc"){
                $luno=Luno::where('transid', $input['transid'])->first();
                return response()->json(['success' => 1, 'message' => 'Address retrieved', 'data' => $luno->address]);
            }
            $input['status']='Duplicate reference';
            $input['transid']=$input['transid'].'_dup';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'Duplicate reference number']);
        }

        if($input['payment_method'] =="general_market"){
            $set=Settings::where('name','general_market')->first();
            if ($set->value < 300) {
                $input['status']='general market is lower than threshold';
                $input['transid']=$input['transid'];
                Serverlog::create($input);
                return response()->json(['success' => 0, 'message' => 'General market balance is lower than threshold']);
            }

            $bugm=DB::table("tbl_generalmarket_blocked user")->get();
            foreach ($bugm as $bu){
                //check for blocked users
                if ($input['user_name']== $bu->user_name) {
                    $input['status']='User suspended';
                    $input['transid']=$input['transid'];
                    Serverlog::create($input);
                    return response()->json(['success' => 0, 'message' => 'error']);
                }
            }

            if ($set->value >= $input['amount']) {
                Serverlog::create($input);
                return $next($request);
            }

            $input['status']='general market is low';
            $input['transid']=$input['transid'];
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'General market balance is low']);
        }

        if($input['payment_method'] =="btc"){
            $input['status']='pending';
            Serverlog::create($input);
            return $next($request);
        }

        if($input['payment_method'] !="wallet"){
            $input['status']='pending';
            Serverlog::create($input);
            return response()->json(['success' => 1, 'message' => 'Transaction executed successfully']);
        }

        $user=User::where('user_name',$input['user_name'])->first();

        if($user->wallet <=0){
            $input['status']='Balance to low';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'Error, wallet balance too low']);
        }
        if($input['amount'] > $user->wallet){
            $input['status']='Balance to low';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'Error, wallet balance too low']);
        }

        $lasttime=Serverlog::where('user_name', $input['user_name'])->orderBy('id', 'desc')->first();
        if(Carbon::now()->diffInMinutes(Carbon::parse($lasttime->date),  false)<0){
            $input['status']='Suspect Fraud';
            Serverlog::create($input);
            $user=User::where('user_name', $input['user_name'])->first();
            $user->wallet-=$input['amount'];
            $user->save();
            return response()->json(['success' => 0, 'message' => 'Suspect Fraud']);
        }

        Serverlog::create($input);
        return $next($request);
    }
}
