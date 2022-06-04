<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Jobs\PushNotificationJob;
use App\Mail\Notification;
use App\Models\PndL;
use App\Models\ResellerPaymentLink;
use App\Models\Transaction;
use App\Models\VirtualAccountClient;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function index(Request $request)
    {

        $users = DB::table('tbl_agents')->orderBy('id', 'desc')->paginate(25);

        $t_users = DB::table('tbl_agents')->count();
        $ac_users = DB::table('tbl_agents')->where([["wallet",">=","50"], ["fraud","=",""]])->count();
        $iac_users = DB::table('tbl_agents')->where([["wallet","<","50"], ["fraud","=",""]])->count();
        $f_users = DB::table('tbl_agents')->where("fraud","!=","")->count();

        $r_users = DB::table('tbl_agents')->where("referral","!=","")->count();

        $a_users = DB::table('tbl_agents')->where("status","=","agent")->count();
        $aca_users = DB::table('tbl_agents')->where([["status","=","agent"], ["wallet",">=","50"]])->count();
        $iaca_users = DB::table('tbl_agents')->where([["status","=","agent"], ["wallet","<","50"]])->count();

        $u_wallet = DB::table('tbl_agents')->where("status","!=","admin")->sum('wallet');
        $fu_wallet = DB::table('tbl_agents')->where([["status","!=","admin"], ["fraud","!=",""] ])->sum('wallet');
        $au_wallet = DB::table('tbl_agents')->where([["status","!=","admin"], ["fraud","=",""], ["wallet",">=","50"] ])->sum('wallet');
        $iau_wallet = DB::table('tbl_agents')->where([["status","!=","admin"], ["fraud","=",""], ["wallet","<","50"] ])->sum('wallet');

        return view('users', ['users' => $users, 't_users'=>$t_users, 'ac_users' =>$ac_users, 'iac_users' => $iac_users, 'f_users'=>$f_users, 'r_users'=>$r_users, 'u_wallet'=>$u_wallet, 'a_users'=>$a_users, 'aca_users'=>$aca_users, 'iaca_users'=>$iaca_users, 'fu_wallet'=>$fu_wallet, 'iau_wallet'=>$iau_wallet, 'au_wallet'=>$au_wallet]);

    }

    public function agents(Request $request)
    {

        $users = DB::table('tbl_agents')->where('status', 'agent')->orderBy('id', 'desc')->get();
        $trans=Transaction::where('date', 'LIKE', '%'.date("Y-m-d").'%')->get();

        return view('agents', ['users' => $users, 'trans'=>$trans]);

    }

    public function resellers(Request $request)
    {
        $users = User::where('status', 'reseller')->orderBy('id', 'desc')->get();

        return view('reseller', ['users' => $users]);
    }

    public function regenerateKey($id)
    {
        $u = User::find($id);
        $key="key_".uniqid().rand().Carbon::now();
        $u->api_key=$key;
        $u->save();

        return redirect()->route('resellers')->with('success', $u->user_name." API Key regenerated successful");
    }

    public function gmblocked(Request $request)
    {

        $users = DB::table('tbl_generalmarket_blocked user')->orderBy('id', 'desc')->get();

        return view('gm_blocked_users', ['users' => $users]);
    }


    public function dormant(Request $request)
    {

        $users = DB::table('tbl_agents')->whereMonth('last_login', '>', '3' )->paginate(10);

        return view('dormant_users', ['users' => $users]);
    }

    public function pending(Request $request)
    {

        $users = User::where([['target', 'like', '%in progress%'], ['document', 1]])->latest()->get();
        $tp = User::where('target', 'like', '%in progress%')->orderBy('id', 'desc')->count();
        $rp = User::where('target', 'like', '%Reseller in progress%')->orderBy('id', 'desc')->count();
        $ap = User::where('target', 'like', '%Agent in progress%')->orderBy('id', 'desc')->count();


        return view('pending_request', ['users' => $users, 'tp' => $tp, 'rp' => $rp, 'ap' => $ap]);

    }

    public function finduser(Request $request){
        $input = $request->all();
        $user_name=$input['user_name'];
        $phoneno=$input['phoneno'];
        $status=$input['status'];
        $wallet=$input['wallet'];
        $email=$input['email'];
        $regdate=$input['regdate'];

        // Instantiates a Query object
        $query = User::Where('user_name', 'LIKE', "%$user_name%")
            ->Where('phoneno', 'LIKE', "%$phoneno%")
            ->Where('email', 'LIKE', "%$email%")
            ->Where('status', 'LIKE', "%$status%")
            ->Where('wallet', 'LIKE', "%$wallet%")
            ->Where('reg_date', 'LIKE', "%$regdate%")
            ->limit(100)
            ->get();

        $cquery = User::Where('user_name', 'LIKE', "%$user_name%")
            ->Where('phoneno', 'LIKE', "%$phoneno%")
            ->Where('email', 'LIKE', "%$email%")
            ->Where('status', 'LIKE', "%$status%")
            ->Where('wallet', 'LIKE', "%$wallet%")
            ->Where('reg_date', 'LIKE', "%$regdate%")
            ->count();

        return view('find_user', ['users' => $query, 'count'=>$cquery, 'result'=>true]);
    }

    public function profile($user)
    {
        $ap = User::where('user_name', $user)->first();

        if(!$ap){
            return redirect('/users')->with("error", "User does not exist");
        }

//        $users = DB::table('tbl_agents')->where('target', 'like', '%in progress%')->orderBy('id', 'desc')->get();
        $tt = Transaction::where('user_name', $user)->count();
        $td = Transaction::where('user_name', $user)->orderBy('id', 'desc')->paginate(25);
        $v = DB::table('tbl_severlog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $tw = DB::table('tbl_wallet')->where('user_name', $user)->count();
        $wd = DB::table('tbl_wallet')->where('user_name', $user)->orderBy('id', 'desc')->paginate(25);
        $tpld = DB::table('tbl_p_nd_l')->where('narration', 'like', '%' . $user . '%')->count();
        $pld = DB::table('tbl_p_nd_l')->where('narration', 'like', '%' . $user . '%')->orderBy('id', 'desc')->get();
        $referrals = User::where('referral', $user)->get();
        $sms = DB::table('tbl_smslog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $email = DB::table('tbl_emaillog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $push = DB::table('tbl_pushnotiflog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $login = DB::table('tbl_login_attempt')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $cypto = DB::table('tbl_luno')->where('user_name', $user)->orderBy('id', 'desc')->get();

        $tat = Transaction::where([['user_name', $user], ['name', 'LIKE', '%airtime']])->count();
        $tdt = Transaction::where([['user_name', $user], ['name', 'LIKE', '%data']])->count();
        $tct = Transaction::where([['user_name', $user], ['name', 'LIKE', '%Card']])->count();
        $tpt = Transaction::where([['user_name', $user], ['name', 'LIKE', '%paytv']])->count();
        $trt = Transaction::where([['user_name', $user], ['name', '=', 'Result Checker']])->count();

        return view('profile', ['user' => $ap, 'tt' => $tt, 'td' => $td, 'tw' => $tw, 'wd' => $wd, 'tpld' => $tpld, 'pld' => $pld, 'referrals' => $referrals, 'version' => $v, 'sms' => $sms, 'email' => $email, 'push'=>$push, 'tat' =>$tat, 'tdt'=>$tdt, 'tpt'=>$tpt, 'tct'=>$tct, 'trt'=>$trt, 'login'=>$login, 'crypto'=>$cypto]);
    }

    public function approve(Request $request)
    {
        $input = $request->all();

        if ($input["type"] == "agent") {
            DB::table('tbl_agents')->where('user_name', $input['user_name'])->update(["status" => "agent", "target" => "Target: Buy 20 data and 20 airtime this month to complete your level."]);

            $ap = User::where('user_name', $input['user_name'])->first();


            $GLOBALS['email'] = $ap->email;

            $data = array('name' => $ap->full_name, 'date' => date("D, d M Y"));
            Mail::send('email_agent', $data, function ($message) {
                $message->to($GLOBALS['email'], 'MCD Agent')->subject('MCD Agent Approval');
                $message->from('info@5starcompany.com.ng', '5Star Company');
            });
        } else if ($input["type"] == "reseller") {
            DB::table('tbl_agents')->where('user_name', $input['user_name'])->update(["status" => "reseller", "target" => "Reseller Activated"]);
        }


        return redirect('profile/' . $input['user_name']);

    }

    public function referral_upgrade(Request $request)
    {
        $input = $request->all();

        if ($input["plan"] == "larvae") {
            $amount=1000;
        }else{
            $amount=5000;
        }
        $u = User::where('user_name', $input['user_name'])->first();

        if(!$u){
            return redirect('/referral_upgrade')->with('error', $input["user_name"]. ' does not exist!');
        } elseif($u->wallet >= $amount){
            $input['name'] = "Referral Upgrade";
            $input['amount'] = $amount;
            $input['status'] = 'successful';
            $input['description'] = "Being amount charged for referral upgrade to ".$input["plan"]." on ".$u->user_name;
            $input['user_name'] = $u->user_name;
            $input['code'] = 'aru';
            $input['i_wallet'] = $u->wallet;
            $wallet = $u->wallet + $amount;
            $input['f_wallet'] = $wallet;
            $input["ip_address"] = "127.0.0.1:A";
            $input["date"] = date("y-m-d H:i:s");
            $input["extra"]='Initiated by ' . Auth::user()->full_name;

            Transaction::create($input);

            $input["type"] = "income";
            $input["narration"] = $input['description'];

            PndL::create($input);

            $u->wallet-=$amount;
            $u->referral=$input["plan"];
            $u->save();

            $GLOBALS['email'] = $u->email;

            $data = array('name' => $u->full_name, 'date' => date("D, d M Y"));
            Mail::send('email_referral_upgrade', $data, function ($message) {
                $message->to($GLOBALS['email'], 'MCD Customer')->subject('MCD Referral Upgrade');
                $message->from('info@5starcompany.com.ng', '5Star Inn Company');
            });

            return redirect('/referral_upgrade')->with('success', $input["user_name"]. ' has been upgraded to '. $input["plan"].' successfully!');
        }else{
            return redirect('/referral_upgrade')->with('error', $input["user_name"]. ' wallet balance is currently low.');
        }
    }

    public function sendsms(Request $request)
    {
        $input = $request->all();


        $sms_id="15658";
        $sms_secret="66Wby95tGM15Wo3uQk1OwiYO3muum4Ds";
        $sms_pass="zEKJKdpxfvuDzYtTZipihelDJQ0NttZ28JMSXbpcHT";
        $sms_senderid="MCD Notification";
        $sms_charges=3;

//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => "http://www.sms.5starcompany.com.ng/smsapi?pd_m=send&id=" . $sms_id . "&secret=" . $sms_secret . "&pass=" . $sms_pass . "&senderID=" . $sms_senderid . "&to_number=" . $input['phoneno'] . "&textmessage=" . $input['message'],
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_CUSTOMREQUEST => "GET",
//            CURLOPT_HTTPHEADER => [
//                "content-type: application/json",
//                "cache-control: no-cache"
//            ],
//        ));
//
//        $response = curl_exec($curl);
//        $err = curl_error($curl);
//
//        if ($err) {
//            // there was an error contacting the SMS Portal
//            die('Curl returned error: ' . $err);
//        }

        Mail::to('odejinmisamuel@gmail.com')->send(new Notification());

//        DB::table('tbl_smslog')->insert(
//            ['user_name' => $input["user_name"], 'message' => $input['message'], 'phoneno' => $input['phoneno'], 'response' => $response]
//        );


        return redirect('profile/' . $input['user_name']);

    }

    public function sendemail(Request $request)
    {
        $input = $request->all();

        $ap = User::where('user_name', $input['user_name'])->first();

        $GLOBALS['email'] = $ap->email;

        $data = array('name' => $ap->full_name, 'messag' => $input['message']);
        Mail::send('email_notification', $data, function ($message) {
            $message->to($GLOBALS['email'], 'MCD Client')->subject('MCD Notification');
            $message->from('info@5starcompany.com.ng', '5Star Company');
        });

        DB::table('tbl_emaillog')->insert(
            ['user_name' => $input["user_name"], 'message' => $input['message'], 'email' => $ap->email, 'response' => 'sent']
        );

        return redirect('profile/' . $input['user_name']);
    }

    public function sendpushnotif(Request $request)
    {
        $input = $request->all();

        $ap = User::where('user_name', $input['user_name'])->first();

        $topic=$ap->user_name;

        $topi=str_replace(" ","", $topic);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n\"to\": \"/topics/".$topi."\",\n\"data\": {\n\t\"extra_information\": \"PLANETF\"\n},\n\"notification\":{\n\t\"title\": \"MCD Notification\",\n\t\"text\":\"". $input['message']."\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=AAAAOW0II6E:APA91bHyum5pMhub2JVHcHnQghuWOdktOuhW9e4ZvmMDudjMZk9y1u71Nr7yl_FZLpsjuC6Hz1Fd49OrWfPYNKpAvahAZ5Rjv0y7IW24nqjYrPnMer8IvTkzZFB5W3hrOHAwbq2EOMOE",
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $json=json_decode($response, true);


        DB::table('tbl_pushnotiflog')->insert(
            ['user_name' => $input["user_name"], 'message' => $input['message'], 'response' => $json['message_id']]
        );

        return redirect('profile/' . $input['user_name']);
    }

    public function addgnews(Request $request)
    {
        $input = $request->all();

        if ($input["user_name"] == "") {
            User::where('user_name','!=',$input["user_name"])->update(['gnews'=>$input["message"]]);

        }else{
            $user=User::where('user_name','=',$input["user_name"])->exists();
            if(!$user){
                return redirect('/gnews')->with('success', 'Username does not exist!');
            }

            User::where('user_name','=',$input["user_name"])->update(['gnews'=>$input["message"]]);
        }

        if(isset($input['push_notification'])){
            PushNotificationJob::dispatch('general_notification', $input['message'], "General Notification");
        }

        return redirect('/gnews')->with('success', 'Message sent successfully!');

    }

    public function agent_list()
    {

        $users=User::where('status', '=', 'agent')->get();

        return view('agent_payment', ['users' => $users, 'alist'=>true]);
    }

    public function agent_confirm(Request $request)
    {

        $input = $request->all();

        $user=User::where('user_name', '=', $input["user_name"])->first();

        if(!$user){
            return back()->with('error', 'Username does not exist!');
        }

        if($user->status != "agent"){
            return back()->with('error', 'User is not an Agent!');
        }

        $tc = Transaction::where([['code', '=', "acp_".Carbon::now()->subMonth()->format('m.y')], ['user_name', '=', $input["user_name"]]])->get();

        if(!$tc->isEmpty()){
            return back()->with('error', 'Payment made already!');
        }


        $trans = Transaction::where([['user_name', '=', $input["user_name"]], ['name', 'NOT LIKE', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', '%'.Carbon::now()->subMonth()->format("Y-m").'%']])->get();
        $trans_count = Transaction::where([['user_name', '=', $input["user_name"]], ['name', 'NOT LIKE', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', '%'.Carbon::now()->subMonth()->format("Y-m").'%']])->count();

        if ($trans->isEmpty()) {
            return back()->with('error', 'No Transaction done last month!');
        }

        return view('agent_payment', ['trans' => $trans, 'count'=> $trans_count, 'user' =>$user, 'val'=>true]);
    }

    public function agent_payment(Request $request)
    {
        $input = $request->all();

        $user=User::where('user_name', $input['user_name'])->first();

        $f=$user->level;

        if($f==1){
            $fa=3;
        }
        if($f==2){
            $fa=5;
        }
        if($f==3){
            $fa=8;
        }
        if($f==4){
            $fa=9;
        }
        if($f==5){
            $fa=10;
        }
        if($f==6){
            $fa=13;
        }
        if($f==7){
            $fa=15;
        }
        if($f==8){
            $fa=20;
        }
        if($f==9){
            $fa=25;
        }
        if($f==10){
            $fa=30;
        }

        $amount=$fa * $input['count'];

        $user=User::where("user_name", "=", $input['user_name'])->first();
        $input["description"]="Being agent commission paid for the month of " . Carbon::now()->subMonth()->format('M, Y') . " with transaction count of " . $input['count'] . ' as a Level ' . $f . ' agent' ;
        $input["name"]="Agent Commission";
        $input["status"]="successful";
        $input["code"]="acp_".Carbon::now()->subMonth()->format('m.y');
        $input["ip_address"]="127.0.0.1";
        $input["amount"]=$amount;
        $input["user_name"]=$input['user_name'];
        $input["i_wallet"]=$user->agent_commision;
        $input["f_wallet"]=$user->agent_commision + $amount;
        $input["extra"]='Initiated by ' . Auth::user()->full_name;
        Transaction::create($input);

        $user->agent_commision =$input["f_wallet"];
        $user->save();

        $input["type"]="expenses";
        $input["gl"]="Agent Commission";
        $input["amount"]=$amount;
        $input["narration"]="Being agent commission on ".$input['user_name']. " for ".Carbon::now()->subMonth()->format('m.y');
        $input["date"]=Carbon::now();

        PndL::create($input);

        return redirect('/agentpayment')->with('success', 'Agent Payment paid successfully to ' . $user->user_name . ' with the sum of ' . $amount);
    }

    public function loginattempt()
    {

        $login = DB::table('tbl_login_attempt')->orderBy('id', 'desc')->paginate(10);

        return view('login_attempts', ['login' => $login]);
    }

    public function vaccounts()
    {
        $datas['accounts'] = VirtualAccountClient::orderBy('id', 'desc')->paginate(10);

        return view('resellers_virtual_accounts', $datas);
    }

    public function paymentLinks()
    {
        $datas['datas'] = ResellerPaymentLink::orderBy('id', 'desc')->paginate(10);

        return view('resellers_payment_links', $datas);
    }

}
