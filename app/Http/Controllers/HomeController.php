<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
//        $data['total_user'] = DB::table('tbl_agents')->get()->count();
        $data['today_user'] = User::where('reg_date', 'LIKE', '%' . Carbon::now()->format('Y-m-d') . '%')->count();
//        $data['active_user'] = DB::table('tbl_agents')->where('wallet', '>=', '1')->get()->count();
//        $data['inactive_user'] = DB::table('tbl_agents')->where('wallet', '<', '1')->get()->count();
//        $data['client'] = DB::table('tbl_agents')->where('status', '=', 'client')->get()->count();
//        $data['agent'] = DB::table('tbl_agents')->where('status', '=', 'agent')->get()->count();
//        $data['reseller'] = DB::table('tbl_agents')->where('status', '=', 'reseller')->get()->count();
        $today = Carbon::now()->format('Y-m-d');
//        $data['online_user'] = DB::table('tbl_agents')->where('last_login', 'LIKE', $today.'%')->get()->count();
//        $data['total_deposits'] = DB::table('tbl_agents')->get()->sum('wallet');
        $data['today_deposits'] = Transaction::where([['name', '=', 'wallet funding'], ['date', 'LIKE', '%' . Carbon::now()->format('Y-m-d') . '%']])->sum('amount');
//        $data['total_transaction'] = DB::table('tbl_transactions')->get()->count();

        $data['today_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->format('Y-m-d') . '%')->count();
        $data['yesterday_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDay()->format('Y-m-d') . '%')->count();
        $data['d2_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDay(2)->format('Y-m-d') . '%')->count();
        $data['d3_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDay(3)->format('Y-m-d') . '%')->count();
        $data['d4_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDay(4)->format('Y-m-d') . '%')->count();
        $data['d5_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDay(5)->format('Y-m-d') . '%')->count();
        $data['d6_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDay(6)->format('Y-m-d') . '%')->count();

//        $data['fail_transaction'] = DB::table('tbl_transactions')->where('status', '=', 'Not Delivered')->orWhere('status', '=', 'not_delivered')->orWhere('status', '=', 'ORDER_CANCELLED')->orWhere('status', '=', 'Invalid Number')->orWhere('status', '=', 'Unsuccessful')->get()->count();
//        $data['successful_transaction'] = DB::table('tbl_transactions')->where('status', '=', 'Delivered')->orWhere('status', '=', 'delivered')->orWhere('status', '=', 'ORDER_RECEIVED')->get()->count();
//        $data['banktransfer'] = DB::table('tbl_transactions')->where('code', '=', 'fund_Banktransfer')->get()->count();
//        $data['banktransfer_today'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Banktransfer'], ['date', 'LIKE', $today.'%' ]])->get()->count();
//        $data['paystack'] = DB::table('tbl_transactions')->where('code', '=', 'fund_Paystack')->get()->count();
//        $data['paystack_today'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Paystack'], ['date', 'LIKE', $today.'%' ]])->get()->count();
//        $data['payant'] = DB::table('tbl_transactions')->where('code', '=', 'fund_Payant')->get()->count();
//        $data['payant_today'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Payant'], ['date', 'LIKE', $today.'%' ]])->get()->count();
//        $data['payant_value'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Rave'],['status', '=', 'successful'],])->sum('amount');
//        $data['rave'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Rave'],['status', '=', 'successful'],])->count();
//        $data['rave_today'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Rave'], ['date', 'LIKE', $today.'%' ]])->count();
//        $data['rave_value'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Rave'],['status', '=', 'successful'],])->sum('amount');
//        $data['total_fund'] = DB::table('tbl_transactions')->where('code', '=', 'fund_Payant')->orWhere('code', '=', 'fund_Paystack')->orWhere('code', '=', 'fund_Banktransfer')->orWhere('code', '=', 'fund_Rave')->count();
        $data['transactions'] = Transaction::orderBy('id', 'DESC')->limit(20)->get();
        $data['users'] = User::orderBy('id', 'DESC')->limit(15)->get();
//        $data['serverlog'] = DB::table('tbl_severlog')->orderBy('id', 'DESC')->limit(10)->get();

        $data['data'] = Transaction::where([['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['airtime'] = Transaction::where([['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['tv'] = Transaction::where([['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['betting'] = Transaction::where([['code', 'like', '%bet%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();

//        $data['autocharge'] = DB::table('tbl_transactions')->where([['name', '=', 'Auto Charge'],['status', '=', 'successful'],])->count();
//        $data['simswap'] = DB::table('tbl_transactions')->where([['code', '=', 'swap'],['status', '=', 'successful'],])->count();
//        $data['walletlogs'] = DB::table('tbl_wallet')->orderBy('id', 'DESC')->limit(9)->get();
        $data['audit_trails'] = DB::table('audit_trail')->orderBy('audit_trail.id', 'DESC')->limit(9)->get();
        $data['p_nd_l'] = DB::table('tbl_p_nd_l')->where([['type', '=', 'income'], ['date', 'LIKE', '%' . Carbon::now()->format('Y-m-d') . '%']])->get()->count();
        $data['allsettings'] = DB::table('tbl_allsettings')->limit(14)->get();
        $data['general_market'] = DB::table('tbl_allsettings')->where("name", "=", "general_market")->first();

        return view('home', $data);
    }
}
