<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\GiveAway;
use App\Models\GiveAwayRequest;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GiveAwayController extends Controller
{
    public function create(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'amount' => 'required',
            'quantity' => 'required',
            'type' => 'required',
            'type_code' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing', 'error' => $validator->errors()->all()]);
        }


        if ($input['amount'] < 1) {
            return response()->json(['success' => 0, 'message' => 'Invalid amount provided']);
        }

        $wallet_bal = Auth::user()->wallet;

        if ($input['amount'] > $wallet_bal) {
            return response()->json(['success' => 0, 'message' => 'Insufficient balance to handle request']);
        }


        if (isset($input["image"])) {

            $image = $input["image"];
            $photo = rand() . ".jpg";

            $decodedImage = base64_decode("$image");
            file_put_contents(storage_path("app/public/giveaway/" . $photo), $decodedImage);

            $input["image"] = "giveaway/" . $photo;
        }

        $input["user_name"] = Auth::user()->user_name;
        $g = GiveAway::create($input);

        $ref = "MCD_" . substr(Auth::user()->user_name, 0, 3) . $g->id;

        $tr['name'] = "Giveaway";
        $tr['description'] = $input['type'] . " " . $input['type_code'] . " " . $input['amount'] . " for " . $input['quantity'] . " people";
        $tr['amount'] = $input['amount'];
        $tr['date'] = Carbon::now();
        $tr['device_details'] = "api";
        $tr['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $tr['i_wallet'] = $wallet_bal;
        $tr['f_wallet'] = $tr['i_wallet'] - $tr['amount'];
        $tr['user_name'] = Auth::user()->user_name;
        $tr['ref'] = $ref . rand();
        $tr['code'] = "giveaway";
        $tr['server'] = "auto";
        $tr['server_response'] = $ref;
        $tr['payment_method'] = "wallet";
        $tr['status'] = "submitted";
        $tr['extra'] = $g->id;
        Transaction::create($tr);

        $user = User::find(Auth::id());
        $user->wallet = $tr['f_wallet'];
        $user->save();


        return response()->json(['success' => 1, 'message' => 'Give away created successfully']);
    }

    public function fetch()
    {
        $ga = GiveAway::latest()->get();
        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $ga]);
    }

    public function request(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'giveaway_id' => 'required',
            'receiver' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing', 'error' => $validator->errors()->all()]);
        }

        $ga = GiveAway::find($input['giveaway_id']);

        if (!$ga) {
            return response()->json(['success' => 0, 'message' => 'Invalid Giveaway']);
        }

        if ($ga->status == 1) {
            return response()->json(['success' => 0, 'message' => 'Giveaway completed earlier']);
        }

        $qq = GiveAwayRequest::where('giveaway_id', $input['giveaway_id'])->count();

        if ($qq >= $ga->quantity) {
            return response()->json(['success' => 0, 'message' => 'No more space for you']);
        }

        //check if am the owner

        //check if i have requested earlier

        $input["user_name"] = Auth::user()->user_name;

        $input["amount"] = $ga->amount / $ga->quantity;

        GiveAwayRequest::create($input);

        return response()->json(['success' => 1, 'message' => 'Request successful. Action will be initiated as soon as people are completed']);
    }
}
