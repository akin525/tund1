<?php

namespace App\Http\Controllers;

use App\Model\Airtime2Cash;
use App\Model\PndL;
use App\Model\Transaction;
use App\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PDF;

class TransactionController extends Controller
{
    public function index(Request $request){

        $data = DB::table('tbl_transactions')->orderBy('id', 'desc')->limit(1000)->get();
        $tt = DB::table('tbl_transactions')->get()->count();
        $ft = DB::table('tbl_transactions')->where([['status', '=', 'cancelled'], ['date', 'like', date('Y-m-d').'%']])->orWhere([['status', '=', 'Unsuccessful'], ['date', 'like', date('Y-m-d').'%']])->orWhere([['status', '=', 'Error'], ['date', 'like', date('Y-m-d').'%']])->get()->count();
        $st = DB::table('tbl_transactions')->where([['status', '=', 'delivered'], ['date', 'like', date('Y-m-d').'%']])->orWhere([['status', '=', 'submitted'], ['date', 'like', date('Y-m-d').'%']])->orWhere([['status', '=', 'API_successful'], ['date', 'like', date('Y-m-d').'%']])->count();
        $rt = DB::table('tbl_transactions')->where([['status', '=', 'reversed'], ['date', 'like', date('Y-m-d').'%']])->count();

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

        return view('transactions', ['data' => $data, 'tt'=>$tt, 'ft'=>$ft, 'st'=>$st, 'rt'=>$rt, 'g_date'=>substr($gdate, 1), 'g_tran'=>substr($gtrans, 1), 'g_wallet'=>substr($gwallet, 1)]);

    }

    public function rechargecard(Request $request){
        $input = $request->all();

        $user = DB::table('tbl_agents')->where('user_name', $input['user_name'])->first();

        if(!$user){
            return redirect()->route('rechargecard')
                ->with('success','User doesnt exist');
        }


/*        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.nellobytesystems.com/APIEPINV1.asp?UserID=CK10123847&APIKey=W5352Q23GDS924D7UA1B84YYY506178I69DDE4JR1ZRAR80FCBQF819D4T7HKI85&MobileNetwork=".$input['network']."&Value=".$input['amount']."&Quantity=".$input['quantity']."&CallBackURL=http://www.5starcompany.com.ng",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "content-type: application/json",
                "cache-control: no-cache"
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($err){
            // there was an error contacting the Paystack API
            die('Curl returned error: ' . $err);
        }*/

//        $response='{"TXN_EPIN":[{"transactionid":"6342727713","transactiondate":"5/14/2020 11:00:00 AM","batchno":"203682","mobilenetwork":"GLO","sno":"680401306984254","pin":"146297803330390","amount":"100"},{"transactionid":"6342727714","transactiondate":"5/14/2020 11:00:00 AM","batchno":"203682","mobilenetwork":"GLO","sno":"680401306984255","pin":"146297845881340","amount":"100"},{"transactionid":"6342727715","transactiondate":"5/14/2020 11:00:00 AM","batchno":"203682","mobilenetwork":"GLO","sno":"680401306984256","pin":"146297693718491","amount":"100"},{"transactionid":"6342727716","transactiondate":"5/14/2020 11:00:00 AM","batchno":"203682","mobilenetwork":"GLO","sno":"680401306984257","pin":"146297614150942","amount":"100"},{"transactionid":"6342727717","transactiondate":"5/14/2020 11:00:00 AM","batchno":"203682","mobilenetwork":"GLO","sno":"680401306984258","pin":"146297845985782","amount":"100"}]}';
//        $response='{"TXN_EPIN":[{"transactionid":"6342615591","transactiondate":"5/14/2020 9:41:00 PM","batchno":"205352","mobilenetwork":"MTN","sno":"00000005099279064","pin":"17287167093507274","amount":"100"},{"transactionid":"6342615594","transactiondate":"5/14/2020 9:41:00 PM","batchno":"205352","mobilenetwork":"MTN","sno":"00000005105116628","pin":"58508681769179769","amount":"100"},{"transactionid":"6342615584","transactiondate":"5/14/2020 9:41:00 PM","batchno":"205352","mobilenetwork":"MTN","sno":"00000005164416614","pin":"50815693195674413","amount":"100"},{"transactionid":"6342615587","transactiondate":"5/14/2020 9:41:00 PM","batchno":"205352","mobilenetwork":"MTN","sno":"00000005208228089","pin":"74256074917601404","amount":"100"},{"transactionid":"6342615588","transactiondate":"5/14/2020 9:41:00 PM","batchno":"205352","mobilenetwork":"MTN","sno":"00000005239979674","pin":"07022003391573100","amount":"100"}]}';
        $response='{"TXN_EPIN":[{"transactionid":"6343954670","transactiondate":"5/22/2020 7:58:00 AM","batchno":"220639","mobilenetwork":"GLO","sno":"780409013324722","pin":"904525101269726","amount":"100"},{"transactionid":"6343954671","transactiondate":"5/22/2020 7:58:00 AM","batchno":"220639","mobilenetwork":"GLO","sno":"780409013324723","pin":"904525074115295","amount":"100"},{"transactionid":"6343954672","transactiondate":"5/22/2020 7:58:00 AM","batchno":"220639","mobilenetwork":"GLO","sno":"780409013324724","pin":"904525395664686","amount":"100"},{"transactionid":"6343954673","transactiondate":"5/22/2020 7:58:00 AM","batchno":"220639","mobilenetwork":"GLO","sno":"780409013324725","pin":"904525223872610","amount":"100"},{"transactionid":"6343954674","transactiondate":"5/22/2020 7:58:00 AM","batchno":"220639","mobilenetwork":"GLO","sno":"780409013324726","pin":"904525463396642","amount":"100"}]}';

        $tranx = json_decode($response, true);

//        echo $response. "<br />";

        $findme = 'TXN_EPIN';
        $pos = strpos($response, $findme);
        // Note our use of ===.  Simply == would not work as expected
        if ($pos === false) {
            return response()->json(['status' => 0, 'message'=>'Error generating recharge card']);
        }else{
            foreach ($tranx['TXN_EPIN'] as $pin){
                DB::table('tbl_rechargecards')->insert(
                    ['pin' => $pin['pin'], 'serial' => $pin['sno'] , 'network' => $pin['mobilenetwork'], 'amount'=>$pin['amount'], 'batchno'=> $pin['batchno'], 'transactionno'=> $pin['transactionid'], 'status'=>'unused', 'user_name'=>$input['user_name']]
                );
            }
        }

//        $cards = DB::table('tbl_rechargecards')->where([['status', 'unused'], ['network', $network] ])->skip(0)->take($quantity)->get();

        $data = ['user' => $user, 'cards'=>$tranx['TXN_EPIN']];
        $pdf = PDF::loadView('pdf_rechargecard', $data);

/*        foreach ($cards as $card){
            DB::table('tbl_rechargecards')->where('id', $card->id)->update(["status"=>"sent", "user_name"=>$user_name]);
        }*/

//        return $pdf->stream($user_name.'_rechargecard.pdf');
        //start generating reference
        $num=date('smdhiy');
        $shuffled = str_shuffle($num);
        $sfinal=substr($shuffled, 0, 8).rand();
        $genId=substr($sfinal, 0, 5);
        //finish generating reference

        $GLOBALS['pathToFile']=public_path().'/'.$input['user_name'].'_rechargecard_'.$input['network'].$genId.'.pdf';

//        return PDF::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');
        return $pdf->save($GLOBALS['pathToFile']);

//        return view('rechargecard', ['user' => $user, 'cards'=>$cards]);

        $GLOBALS['email']=$user->email;

        $data = array('date'=>date("D, d M Y"));
        Mail::send('mail_rechargecard', $data, function($message) {
            $message->to($GLOBALS['email'], 'MCD Agent')->subject('MCD Recharge Card');
            $message->from('info@5starcompany.com.ng','5Star Company');
            $message->attach($GLOBALS['pathToFile']);
        });

        return redirect()->route('rechargecard')
            ->with('success','Recharge sent successfully to '.$user->email . ' @ '. $user->user_name);
    }

    public function rechargemanual(Request $request)
    {
        $user_name="Festus@d";
        $quantity=5;
        $network="MTN";
        $amount=100;

        $user = DB::table('tbl_agents')->where('user_name', $user_name)->first();

        if (!$user) {
            return redirect()->route('rechargecard')
                ->with('success', 'User doesnt exist');
        }

        $cards = DB::table('tbl_rechargecards')->where([['status', 'unused'], ['network', $network], ['amount', $amount] ])->skip(0)->take($quantity)->get();

        $data = ['user' => $user, 'cards'=>$cards];
        $pdf = PDF::loadView('pdf_rechargecard', $data);

                foreach ($cards as $card){
                    DB::table('tbl_rechargecards')->where('id', $card->id)->update(["status"=>"sent", "user_name"=>$user_name]);
                }

        return $pdf->stream($user_name.'_rechargecard.pdf');

    }


    public function monnify(Request $request){
        $input = $request->all();

        DB::table('monnify')->insert(
            ['request' => $request, 'input'=>'hello']
        );
    }

    function hook(Request $request){
        $input = $request->all();

        echo $request;
        echo "<p> </p>";
        $data1= implode($input);
        echo "<p> </p>";
        $data2= json_encode($input);
//        echo $data;

        DB::table('test')->insert(['name'=> 'webhook', 'request'=>$request, 'data1'=>$data1, 'data2'=>$data2]);
    }

    public function addtransaction(Request $request){
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'network'      => 'required',
            'amount' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes())
        {
            $sms_id="15658";
            $sms_secret="66Wby95tGM15Wo3uQk1OwiYO3muum4Ds";
            $sms_pass="zEKJKdpxfvuDzYtTZipihelDJQ0NttZ28JMSXbpcHT";
            $sms_senderid="MCD Transaction";
            $sms_charges=3;
            $charge_treshold=2000;
            $charges=50;
            $amount=$input["amount"];
            $user= User::where('user_name', $input["user_name"])->first();
            $sms_description="Dear ".$input['user_name'].", a transaction of ".$input["network"]. " #".$amount." on ".$input["phoneno"]. " just occured on your account by admin. Regards.";

            if($user){
                $amt=0.02 * $input["amount"];
                $input["amount"] -= $amt;

                if($user->wallet<$input["amount"]){
                    return redirect('/adddatatransaction')->with('success', $input["user_name"]. ' wallet balance is too low!');
                }

                $input["description"]=$input["user_name"] . " buy airtime_".$input["network"]. "_".$amount. " on ".$input["phoneno"]. " using wallet";
                $input["i_wallet"]=$user->wallet;
                $input["f_wallet"]=$input["i_wallet"] - $input["amount"];
                $input["ip_address"]="127.0.0.1";
                $input["code"]="airtime_".$input["network"]. "_".$amount;
                $input["status"]="delivered";
                $input["date"]=date("y-m-d H:i:s");
                $input["name"]=$input["network"]. "airtime";
                $input["extra"]='Initiated by ' . Auth::user()->full_name;

                Transaction::create($input);

                /*$input["description"]="Being sms charge";
                $input["name"]="SMS Charge";
                $input["amount"]=$sms_charges;
                $input["code"]="smsc";
                $input["i_wallet"]=$input["f_wallet"];
                $input["f_wallet"]=$input["f_wallet"] - $sms_charges;

                Transaction::create($input);

                $amount+=$sms_charges;*/

                $user->wallet-=$amount;
                $user->save();

                /*$curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://www.sms.5starcompany.com.ng/smsapi?pd_m=send&id=".$sms_id."&secret=".$sms_secret."&pass=".$sms_pass."&senderID=".$sms_senderid."&to_number=".$user->phoneno."&textmessage=".$sms_description,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        "content-type: application/json",
                        "cache-control: no-cache"
                    ],
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                if($err){
                    // there was an error contacting the SMS Portal
                    die('Curl returned error: ' . $err);
                }


                DB::table('tbl_smslog')->insert(
                    ['user_name' => $input["user_name"], 'message' => $sms_description, 'phoneno' => $user->phoneno, 'response' => $response]
                );*/

                $topic=$user->user_name;

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
                    CURLOPT_POSTFIELDS =>"{\n\"to\": \"/topics/".$topi."\",\n\"data\": {\n\t\"extra_information\": \"Mega Cheap Data\"\n},\n\"notification\":{\n\t\"title\": \"MCD Notification\",\n\t\"text\":\"". $sms_description."\"\n\t}\n}\n",
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: key=AAAAOW0II6E:APA91bHyum5pMhub2JVHcHnQghuWOdktOuhW9e4ZvmMDudjMZk9y1u71Nr7yl_FZLpsjuC6Hz1Fd49OrWfPYNKpAvahAZ5Rjv0y7IW24nqjYrPnMer8IvTkzZFB5W3hrOHAwbq2EOMOE",
                        "Content-Type: application/json",
                        "Content-Type: text/plain"
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
//                echo $response;


                return redirect('/addtransaction')->with('success', $input["user_name"]. ' transaction added successfully!');
            }else{
                $validator->errors()->add('username', 'The username does not exist!');

                return redirect('/addtransaction')
                    ->withErrors($validator)
                    ->withInput($input);
            }

        }else{

            return redirect('/addtransaction')
                ->withErrors($validator)
                ->withInput($input);
//            return response()->json(['status'=> 0, 'message'=>'Unable to login with errors', 'error' => $validator->errors()]);;
        }
    }

    public function addtransaction_data(Request $request){
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'network'      => 'required',
            'plan'      => 'required',
            'amount' => 'required|int|min:2');

        $validator = Validator::make($input, $rules);

        if ($validator->passes())
        {

            $amount=$input["amount"];
            $user= User::where('user_name', $input["user_name"])->first();

            if($user){
                if($user->wallet<$input["amount"]){
                    return redirect('/adddatatransaction')->with('success', $input["user_name"]. ' wallet balance is too low!');
                }
                $input["description"]=$input["user_name"] . " buy data_".$input["network"]. "_".$input["plan"]. " on ".$input["phoneno"]. " using wallet";
                $input["i_wallet"]=$user->wallet;
                $input["f_wallet"]=$input["i_wallet"] - $input["amount"];
                $input["ip_address"]="127.0.0.1";
                $input["code"]="data_".$input["network"]. "_".$input["plan"];
                $input["status"]="delivered";
                $input["date"]=date("y-m-d H:i:s");
                $input["name"]=$input["network"]. "data";
                $input["extra"]='Initiated by ' . Auth::user()->full_name;

                Transaction::create($input);

                $user->wallet-=$input["amount"];
                $user->save();

                return redirect('/adddatatransaction')->with('success', $input["user_name"]. ' transaction added successfully!');
            }else{
                $validator->errors()->add('username', 'The username does not exist!');

                return redirect('/adddatatransaction')
                    ->withErrors($validator)
                    ->withInput($input);
            }

        }else{

            return redirect('/adddatatransaction')
                ->withErrors($validator)
                ->withInput($input);
//            return response()->json(['status'=> 0, 'message'=>'Unable to login with errors', 'error' => $validator->errors()]);;
        }
    }

    public function reversal_confirm(Request $request)
    {
        $input = $request->all();

        $tran = Transaction::where('id', '=', $input["id"])->first();

        if (!$tran) {
            return redirect('/reversal')->with('success', 'Transaction doesnt exist!');

        }
                return view('reversal', ['data' => $tran, 'val'=>true]);
    }

    public function reverse(Request $request, $id)
    {
        $input = $request->all();

        $tran = Transaction::find($id);
        $tran->status="reversed";
        $tran->save();


        $user=User::where("user_name", "=", $tran->user_name)->first();
        $input["description"]="Being reversal of " . $tran->description;
        $input["name"]="Reversal";
        $input["status"]="successful";
        $input["code"]="reversal";
        $input["amount"]=$tran->amount;
        $input["user_name"]=$tran->user_name;
        $input["i_wallet"]=$user->wallet;
        $input["f_wallet"]=$user->wallet + $tran->amount;
        $input["extra"]='Initiated by ' . Auth::user()->full_name;

        $user->update(["wallet"=> $user->wallet + $tran->amount]);
        Transaction::create($input);

        return redirect('/reversal')->with('success', 'Transaction reversed successfully!');

    }

    public function airtime2cash()
    {
        $datas=Airtime2Cash::where('receiver', '=', 'wallet')->orderBy('id', 'desc')->limit(20)->get();

        return view('airtime_cash', ['datas' => $datas, 'alist'=>true]);
    }

    public function airtime2cashpayment(Request $request)
    {
        $ref=Airtime2Cash::where('ref',$request->input('ref'))->first();

        if(!$ref){
            return back()->with('error', 'Invalid Reference Number!');
        }

        if($ref->status=="successful"){
            return back()->with('error', 'Payment made already!');
        }

        $r=0.2 * $ref->amount;
        $r_amount=$ref->amount - $r;

        $user=User::where("user_name", "=", $ref->user_name)->first();

        $input["description"]=$ref->user_name ." wallet funded using Airtime2Wallet with the sum of #". $ref->amount ." Ref=>" . $ref->ref;
        $input["name"]="wallet funding";
        $input["status"]="successful";
        $input["code"]="fund_a2w";
        $input["amount"]=$r_amount;
        $input["user_name"]=$ref->user_name;
        $input["i_wallet"]=$user->wallet;
        $input["f_wallet"]=$user->wallet + $r_amount;
        $input["extra"]='Initiated by ' . Auth::user()->full_name;

        $ref->status="successful";
        $ref->save();
        $user->update(["wallet"=> $user->wallet + $r_amount]);
        Transaction::create($input);

        return redirect('/airtime2cash')->with('success', 'Transaction successful!');
    }

}
