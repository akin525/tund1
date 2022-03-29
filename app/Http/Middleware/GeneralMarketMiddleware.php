<?php

namespace App\Http\Middleware;

use App\Models\Serverlog;
use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GeneralMarketMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $input = $request->all();

        $input['user_name'] = Auth::user()->user_name;
        $input['phone'] = $input['number'];

        if ($input['payment'] == "general_market") {

            $setg = Settings::where('name', 'pay_gm')->first();
            if ($setg->value != 1) {
                return response()->json(['success' => 0, 'message' => 'General market service is currently unavailable. Try later']);
            }

            $set = Settings::where('name', 'general_market')->first();
            if ($set->value < 300) {
                $input['status'] = 'general market is lower than threshold';
                Serverlog::create($input);
                return response()->json(['success' => 0, 'message' => 'General market balance is lower than threshold']);
            }

            $bugm = DB::table("tbl_generalmarket_blocked user")->get();
            foreach ($bugm as $bu) {
                //check for blocked users
                if ($input['user_name'] == $bu->user_name) {
                    $input['status'] = 'User suspended';
                    Serverlog::create($input);
                    return response()->json(['success' => 0, 'message' => 'error']);
                }
            }
        }

        return $next($request);
    }
}
