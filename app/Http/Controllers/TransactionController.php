<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request){

        $data = DB::table('tbl_transactions')->orderBy('id', 'desc')->paginate(30);
        $tt = DB::table('tbl_transactions')->get()->count();
        $ft = DB::table('tbl_transactions')->where('status', '=', 'Not Delivered')->orWhere('status', '=', 'not_delivered')->orWhere('status', '=', 'ORDER_CANCELLED')->orWhere('status', '=', 'Invalid Number')->orWhere('status', '=', 'Unsuccessful')->orWhere('status', '=', 'Error')->get()->count();
        $st = DB::table('tbl_transactions')->where('status', '=', 'Delivered')->orWhere('status', '=', 'delivered')->orWhere('status', '=', 'ORDER_RECEIVED')->orWhere('status', '=', 'ORDER_COMPLETED')->get()->count();

        $mutable = Carbon::now();
        $gdate="";
        $gtrans="";
        $gwallet="";
        for($x = 0; $x <= 7; $x++){
            $modifiedImmutable = CarbonImmutable::now()->add('-'.$x, 'day');
            $imdf =substr($modifiedImmutable, 0, 10);
            $gt = DB::table('tbl_transactions')
                ->where([['status', '=', 'delivered']])
                ->whereDate('date', $imdf)
                ->count();

            $ft = DB::table('tbl_transactions')
                ->where([['name', '=', 'wallet funding']])
                ->whereDate('date', $imdf)
                ->count();

            $imdf =substr($modifiedImmutable, 8, 2);
                $gdate = $gdate . ", " . $imdf;
                $gtrans = $gtrans . "," . $gt;
                $gwallet = $gwallet . "," . $ft;

        }

        return view('transactions', ['data' => $data, 'tt'=>$tt, 'ft'=>$ft, 'st'=>$st, 'g_date'=>substr($gdate, 1), 'g_tran'=>substr($gtrans, 1), 'g_wallet'=>substr($gwallet, 1)]);

    }

    public function rechargecard(Request $request){

        $users = DB::table('tbl_agents')->where('status', 'reseller')->orderBy('id', 'desc')->get();
        $user = DB::table('tbl_agents')->where('user_name', 'samji')->first();

        return view('rechargecard', ['user' => $user]);
    }

    public function monnify(Request $request){
        $input = $request->all();

        DB::table('monnify')->insert(
            ['request' => $request, 'input'=>$input]
        );
    }

}
