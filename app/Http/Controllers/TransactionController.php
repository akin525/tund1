<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use PDF;

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
        $input = $request->all();

        $user = DB::table('tbl_agents')->where('user_name', $input['user_name'])->first();

        if(!$user){
            return redirect()->route('rechargecard')
                ->with('success','User doesnt exist');
        }


        $curl = curl_init();
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
        }

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
                    ['pin' => $pin->pin, 'serial' => $pin->sno , 'network' => $pin->mobilenetwork, 'amount'=>$pin->amount, 'status'=>'sent', 'user_name'=>$input['user_name']]
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

    public function monnify(Request $request){
        $input = $request->all();

        DB::table('monnify')->insert(
            ['request' => $request, 'input'=>$input]
        );
    }

}
