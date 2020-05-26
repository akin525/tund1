<?php

namespace App\Http\Controllers;

use App\Model\Transaction;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use App\Mail\Notification;

class UsersController extends Controller
{
    public function index(Request $request)
    {

        $users = DB::table('tbl_agents')->orderBy('id', 'desc')->limit(1000)->get();

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

        $users = DB::table('tbl_agents')->where('status', 'reseller')->orderBy('id', 'desc')->get();

        return view('reseller', ['users' => $users]);
    }

    public function pending(Request $request)
    {

        $users = DB::table('tbl_agents')->where('target', 'like', '%in progress%')->orderBy('id', 'desc')->get();
        $tp = DB::table('tbl_agents')->where('target', 'like', '%in progress%')->orderBy('id', 'desc')->count();
        $rp = DB::table('tbl_agents')->where('target', 'like', '%Reseller in progress%')->orderBy('id', 'desc')->count();
        $ap = DB::table('tbl_agents')->where('target', 'like', '%Agent in progress%')->orderBy('id', 'desc')->count();


        return view('pending_request', ['users' => $users, 'tp' => $tp, 'rp' => $rp, 'ap' => $ap]);

    }

    public function profile($user)
    {

//        echo $user;

//        $users = DB::table('tbl_agents')->where('target', 'like', '%in progress%')->orderBy('id', 'desc')->get();
        $tt = Transaction::where('user_name', $user)->count();
        $td = Transaction::where('user_name', $user)->orderBy('id', 'desc')->get();
        $v = DB::table('tbl_severlog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $tw = DB::table('tbl_wallet')->where('user_name', $user)->count();
        $wd = DB::table('tbl_wallet')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $tpld = DB::table('tbl_p_nd_l')->where('narration', 'like', '%' . $user . '%')->count();
        $pld = DB::table('tbl_p_nd_l')->where('narration', 'like', '%' . $user . '%')->orderBy('id', 'desc')->get();
        $ap = User::where('user_name', $user)->first();
        $referrals = User::where('referral', $user)->get();
        $sms = DB::table('tbl_smslog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $email = DB::table('tbl_emaillog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $push = DB::table('tbl_pushnotiflog')->where('user_name', $user)->orderBy('id', 'desc')->get();


        return view('profile', ['user' => $ap, 'tt' => $tt, 'td' => $td, 'tw' => $tw, 'wd' => $wd, 'tpld' => $tpld, 'pld' => $pld, 'referrals' => $referrals, 'version' => $v, 'sms' => $sms, 'email' => $email, 'push'=>$push]);

    }

    public function approve(Request $request)
    {
        $input = $request->all();

        if ($input["type"] == "agent") {
            DB::table('tbl_agents')->where('user_name', $input['user_name'])->update(["status" => "agent", "target" => "Target: Buy 60 data and 30 airtime this month to complete your level."]);

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
        $mail=Mail::send('email_notification', $data, function ($message) {
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
            CURLOPT_POSTFIELDS =>"{\n\"to\": \"/topics/".$topi."\",\n\"data\": {\n\t\"extra_information\": \"Mega Cheap Data\"\n},\n\"notification\":{\n\t\"title\": \"MCD Notification\",\n\t\"text\":\"". $input['message']."\"\n\t}\n}\n",
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

        return redirect('/gnews')->with('success', 'Message sent successfully!');

    }
}
