<?php

namespace App\Http\Controllers;

use App\Models\GiveAway;
use App\Models\Transaction;
use App\Models\VirtualAccountClient;
use App\Models\Withdraw;
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
        $today = Carbon::now()->format('Y-m-d');
        $data['today_user'] = User::where('reg_date', 'LIKE', '%' . $today . '%')->count();

        $data['today_deposits'] = Transaction::where([['name', '=', 'wallet funding'], ['date', 'LIKE', '%' . $today . '%']])->sum('amount');

//        $data['today_transaction'] = Transaction::where('date', 'LIKE', '%' . $today . '%')->count();
//        $data['yesterday_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDay()->format('Y-m-d') . '%')->count();
//        $data['d2_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDays(2)->format('Y-m-d') . '%')->count();
//        $data['d3_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDays(3)->format('Y-m-d') . '%')->count();
//        $data['d4_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDays(4)->format('Y-m-d') . '%')->count();
//        $data['d5_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDays(5)->format('Y-m-d') . '%')->count();
//        $data['d6_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDays(6)->format('Y-m-d') . '%')->count();

        $data['users'] = User::orderBy('id', 'DESC')->limit(15)->get();

        $data['data'] = Transaction::where([['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['airtime'] = Transaction::where([['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['tv'] = Transaction::where([['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['betting'] = Transaction::where([['code', 'like', '%bet%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['electricity'] = Transaction::where([['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['rch'] = Transaction::where([['code', 'like', '%rch%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['upgrade'] = Transaction::where([['code', 'like', '%aru%'], ['status', 'like', 'successful'], ['date', 'LIKE', $today . '%']])->count();
        $data['airtime2cash'] = Transaction::where([['code', 'like', '%a2b%'], ['status', 'like', 'successful'], ['date', 'LIKE', $today . '%']])->count();
        $data['airtime2wallet'] = Transaction::where([['code', 'like', '%a2w%'], ['status', 'like', 'successful'], ['date', 'LIKE', $today . '%']])->count();
        $data['virtualaccount'] = VirtualAccountClient::where([['created_at', 'LIKE', $today . '%']])->count();
        $data['withdraw'] = Withdraw::where([['created_at', 'LIKE', $today . '%']])->count();
        $data['giveaway'] = GiveAway::where([['created_at', 'LIKE', $today . '%']])->count();

        $data['p_nd_l'] = DB::table('tbl_p_nd_l')->where([['type', '=', 'income'], ['date', 'LIKE', '%' . $today . '%']])->get()->count();
        $data['allsettings'] = DB::table('tbl_allsettings')->limit(14)->get();
        $data['general_market'] = DB::table('tbl_allsettings')->where("name", "=", "general_market")->first();

        return view('home', $data);
    }
}
