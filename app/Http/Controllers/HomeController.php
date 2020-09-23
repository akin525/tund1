<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use DB;
use Mail;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['total_user'] = DB::table('tbl_agents')->get()->count();
        $data['today_user'] = DB::table('tbl_agents')->where('reg_date', 'LIKE', '%'.Carbon::now()->format('Y-m-d').'%')->count();
        $data['active_user'] = DB::table('tbl_agents')->where('wallet', '>=', '1')->get()->count();
        $data['inactive_user'] = DB::table('tbl_agents')->where('wallet', '<', '1')->get()->count();
        $data['client'] = DB::table('tbl_agents')->where('status', '=', 'client')->get()->count();
        $data['agent'] = DB::table('tbl_agents')->where('status', '=', 'agent')->get()->count();
        $data['reseller'] = DB::table('tbl_agents')->where('status', '=', 'reseller')->get()->count();
        $today =  Carbon::now()->format('Y-m-d');
        $data['online_user'] = DB::table('tbl_agents')->where('last_login', 'LIKE', $today.'%')->get()->count();
        $data['total_deposits'] = DB::table('tbl_agents')->get()->sum('wallet');
        $data['today_deposits'] = DB::table('tbl_transactions')->where([['name', '=', 'wallet funding'], ['date', 'LIKE', '%'.Carbon::now()->format('Y-m-d').'%']])->sum('amount');
        $data['total_transaction'] = DB::table('tbl_transactions')->get()->count();
        $data['today_transaction'] = DB::table('tbl_transactions')->where('date', 'LIKE', '%'.Carbon::now()->format('Y-m-d').'%')->count();
        $data['yesterday_transaction'] = DB::table('tbl_transactions')->where('date', 'LIKE', '%'.Carbon::now()->subDay()->format('Y-m-d').'%')->count();
        $data['d2_transaction'] = DB::table('tbl_transactions')->where('date', 'LIKE', '%'.Carbon::now()->subDay(2)->format('Y-m-d').'%')->count();
        $data['d3_transaction'] = DB::table('tbl_transactions')->where('date', 'LIKE', '%'.Carbon::now()->subDay(3)->format('Y-m-d').'%')->count();
        $data['d4_transaction'] = DB::table('tbl_transactions')->where('date', 'LIKE', '%'.Carbon::now()->subDay(4)->format('Y-m-d').'%')->count();
        $data['d5_transaction'] = DB::table('tbl_transactions')->where('date', 'LIKE', '%'.Carbon::now()->subDay(5)->format('Y-m-d').'%')->count();
        $data['d6_transaction'] = DB::table('tbl_transactions')->where('date', 'LIKE', '%'.Carbon::now()->subDay(6)->format('Y-m-d').'%')->count();
        $data['fail_transaction'] = DB::table('tbl_transactions')->where('status', '=', 'Not Delivered')->orWhere('status', '=', 'not_delivered')->orWhere('status', '=', 'ORDER_CANCELLED')->orWhere('status', '=', 'Invalid Number')->orWhere('status', '=', 'Unsuccessful')->get()->count();
        $data['successful_transaction'] = DB::table('tbl_transactions')->where('status', '=', 'Delivered')->orWhere('status', '=', 'delivered')->orWhere('status', '=', 'ORDER_RECEIVED')->get()->count();
        $data['banktransfer'] = DB::table('tbl_transactions')->where('code', '=', 'fund_Banktransfer')->get()->count();
        $data['banktransfer_today'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Banktransfer'], ['date', 'LIKE', $today.'%' ]])->get()->count();
        $data['paystack'] = DB::table('tbl_transactions')->where('code', '=', 'fund_Paystack')->get()->count();
        $data['paystack_today'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Paystack'], ['date', 'LIKE', $today.'%' ]])->get()->count();
        $data['payant'] = DB::table('tbl_transactions')->where('code', '=', 'fund_Payant')->get()->count();
        $data['payant_today'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Payant'], ['date', 'LIKE', $today.'%' ]])->get()->count();
        $data['payant_value'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Rave'],['status', '=', 'successful'],])->sum('amount');
        $data['rave'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Rave'],['status', '=', 'successful'],])->count();
        $data['rave_today'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Rave'], ['date', 'LIKE', $today.'%' ]])->count();
        $data['rave_value'] = DB::table('tbl_transactions')->where([['code', '=', 'fund_Rave'],['status', '=', 'successful'],])->sum('amount');
        $data['total_fund'] = DB::table('tbl_transactions')->where('code', '=', 'fund_Payant')->orWhere('code', '=', 'fund_Paystack')->orWhere('code', '=', 'fund_Banktransfer')->orWhere('code', '=', 'fund_Rave')->count();
        $data['transactions'] = DB::table('tbl_transactions')->orderBy('id', 'DESC')->limit(20)->get();
        $data['users'] = DB::table('tbl_agents')->orderBy('id', 'DESC')->limit(15)->get();
        $data['serverlog'] = DB::table('tbl_severlog')->orderBy('id', 'DESC')->limit(10)->get();
        $data['data'] = DB::table('tbl_transactions')->where([['name', 'like', '%data%'],['status', '=', 'delivered'],])->orWhere([['name', 'like', '%data%'],['status', '=', 'Delivered'],])->orWhere([['name', 'like', '%data%'],['status', '=', 'ORDER_RECEIVED'],])->count();
        $data['airtime'] = DB::table('tbl_transactions')->where([['name', 'like', '%airtime%'],['status', '=', 'delivered'],])->orWhere([['name', 'like', '%airtime%'],['status', '=', 'Delivered'],])->orWhere([['name', 'like', '%airtime%'],['status', '=', 'ORDER_RECEIVED'],])->count();
        $data['tv'] = DB::table('tbl_transactions')->where([['code', 'like', '%tv%'],['status', 'like', 'delivered'],])->count();
        $data['autocharge'] = DB::table('tbl_transactions')->where([['name', '=', 'Auto Charge'],['status', '=', 'successful'],])->count();
        $data['simswap'] = DB::table('tbl_transactions')->where([['code', '=', 'swap'],['status', '=', 'successful'],])->count();
        $data['walletlogs'] = DB::table('tbl_wallet')->orderBy('id', 'DESC')->limit(9)->get();
        $data['audit_trails'] = DB::table('audit_trail')->orderBy('audit_trail.id', 'DESC')->limit(9)->get();
        $data['p_nd_l'] = DB::table('tbl_p_nd_l')->where([['type','=','income'],['date', 'LIKE', '%'.Carbon::now()->format('Y-m-d').'%']])->get()->count();
        $data['allsettings'] = DB::table('tbl_allsettings')->limit(14)->get();
        $data['general_market'] = DB::table('tbl_allsettings')->where("name", "=", "general_market")->first();



        $url="https://mobilenig.com/api/balance.php";
        $myvars ="username=samji10&password=Emmanuel@10";

        //$data['server1'] = file_get_contents($url.'?'.$myvars);

        $url="https://www.nellobytesystems.com/APIWalletBalanceV1.asp";
        $myvars ="UserID=CK10123847&APIKey=W5352Q23GDS924D7UA1B84YYY506178I69DDE4JR1ZRAR80FCBQF819D4T7HKI85";

        /**
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, 0);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $data['server2'] = curl_exec( $ch );
        */

        // Convert JSON string to Array
        //$data['server2'] = json_decode($result);
       // Dump all data of the Array

        //$data['server2']=$someArray["balance"]; // Access Array data

        /**

        $string= file_get_contents($url.'?'.$myvars);
        $string= str_replace('<script type="text/javascript">','',$string);
        $string= str_replace('if (top.location','',$string);
        $string= str_replace('p.location.href = document.location.hre','',$string);
        $string= str_replace('!= location)','',$string);
        $string= str_replace('</script>','',$string);
        $data['server2']= str_replace('{ //tof; }','',$string);




        $url="https://minitechs.com.ng/api/vtu.php";
        $myvars ="username=samji10&password=Emmanuel@10";

        $data['server3'] = file_get_contents($url.'?'.$myvars);

        //$data['servers_fund']=$data['server1'] + $data['server2'] + $data['server3'];

        //$data['liquidity']=($data['servers_fund'] / $data['total_deposits']) * 100;

        */






        return view('home', $data);
    }
}
