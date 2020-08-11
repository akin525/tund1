<?php

namespace App\Http\Middleware;

use App\Model\Serverlog;
use App\Model\Settings;
use App\User;
use Closure;

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

        $re=Serverlog::where('transid',$input['transid'])->first();

        if($re){
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

            if ($set->value >= $input['amount']) {
                Serverlog::create($input);
                return $next($request);
            }

            $input['status']='general market is low';
            $input['transid']=$input['transid'];
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'General market balance is low']);
        }

        if($input['payment_method'] !="wallet"){
            $input['status']='pending';
            Serverlog::create($input);
            return response()->json(['success' => 1, 'message' => 'Transaction executed successfully']);
        }

        $user=User::where('user_name',$input['user_name'])->first();

        if($user->wallet<$input['amount']){
            $input['status']='Balance to low';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'Error, wallet balance too low']);
        }

        Serverlog::create($input);
        return $next($request);
    }
}
