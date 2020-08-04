<?php

namespace App\Http\Middleware;

use App\Model\Serverlog;
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
            return response()->json(['status' => 0, 'message' => 'Error, invalid request']);
        }

        $user=User::where('user_name',$input['user_name'])->first();

        if($user->wallet<$input['amount']){
            $input['status']='Balance to low';
            Serverlog::create($input);
            return response()->json(['status' => 0, 'message' => 'Error, wallet balance too low']);
        }

        Serverlog::create($input);
        return $next($request);
    }
}
