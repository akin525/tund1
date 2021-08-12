<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class ResellerAuth
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
        $key=$request->header('Authorization');

        if($key==null){
            return response()->json(['status' => 0, 'message' => 'Kindly add the APIKey you obtain in your header request. Kindly contact us on whatsapp@07011223737']);
        }

        $us=User::where("api_key", $key)->first();
        if(!$us){
            return response()->json(['status' => 0, 'message' => 'Invalid API key. Kindly contact us on whatsapp@07011223737']);
        }
        return $next($request);
    }
}
