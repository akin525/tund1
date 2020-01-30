<?php

namespace App\Http\Controllers;

use App\model\Transaction;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\View;

class UsersController extends Controller
{
    public function index(Request $request){

    	 $users = DB::table('tbl_agents')->orderBy('id', 'desc')->paginate(500);

        return view('users', ['users' => $users]);

    }

    public function agents(Request $request){

    	 $users = DB::table('tbl_agents')->where('status', 'agent')->orderBy('id', 'desc')->get();

        return view('agents', ['users' => $users]);

    }

    public function resellers(Request $request){

        $users = DB::table('tbl_agents')->where('status', 'reseller')->orderBy('id', 'desc')->get();

        return view('reseller', ['users' => $users]);
    }

    public function pending(Request $request){

        $users = DB::table('tbl_agents')->where('target', 'like', '%in progress%')->orderBy('id', 'desc')->get();
        $tp = DB::table('tbl_agents')->where('target', 'like', '%in progress%')->orderBy('id', 'desc')->count();
        $rp = DB::table('tbl_agents')->where('target', 'like', '%Reseller in progress%')->orderBy('id', 'desc')->count();
        $ap = DB::table('tbl_agents')->where('target', 'like', '%Agent in progress%')->orderBy('id', 'desc')->count();


        return view('pending_request', ['users' => $users, 'tp'=>$tp, 'rp'=>$rp, 'ap'=>$ap]);

    }

    public function profile($user){

//        echo $user;

//        $users = DB::table('tbl_agents')->where('target', 'like', '%in progress%')->orderBy('id', 'desc')->get();
        $tt = Transaction::where('user_name', $user)->count();
        $td = Transaction::where('user_name', $user)->orderBy('id', 'desc')->get();
        $v = DB::table('tbl_severlog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $tw = DB::table('tbl_wallet')->where('user_name', $user)->count();
        $wd = DB::table('tbl_wallet')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $tpld = DB::table('tbl_p_nd_l')->where('narration', 'like', '%'.$user.'%')->count();
        $pld = DB::table('tbl_p_nd_l')->where('narration', 'like', '%'.$user.'%')->orderBy('id', 'desc')->get();
        $ap = User::where('user_name', $user)->first();
        $referrals = User::where('referral', $user)->get();


        return view('profile', ['user'=>$ap, 'tt'=>$tt, 'td'=>$td, 'tw'=>$tw, 'wd'=>$wd, 'tpld'=>$tpld, 'pld'=>$pld, 'referrals'=>$referrals, 'version'=>$v]);

    }

    public function approve(Request $request){
        $input = $request->all();

        if($input["type"]=="agent"){
            DB::table('tbl_agents')->where('user_name', $input['user_name'])->update(["status"=>"agent", "target"=>"Target: Buy 60 data and 30 airtime this month to complete your level."]);
        }else if($input["type"]=="reseller"){
            DB::table('tbl_agents')->where('user_name', $input['user_name'])->update(["status"=>"reseller", "target"=>"Reseller Activated"]);
        }


        return redirect('profile/'.$input['user_name']);

    }
}
