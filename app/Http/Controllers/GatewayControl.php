<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class GatewayControl
{
    public function gateway(Request $request)
    {
        $payment = settings::get();
        $status = 1 || 0;
//    $status=0;

        return view('gateway', compact('payment', 'status'));

    }

    public function updategateway(Request $request)
    {
        $input = $request->all();

        $payment1 = settings::where('id', $request->id)->first();
        $status = "";
        if ($payment1->status == "1") {
            $status = "0";
        } else {
            $status = "1";
        }

        $payment1->status = $status;
        $payment1->save();
        $msg = "Gatepayment Update Successfully";
        $payment = settings::get();

        return view('gateway', compact('msg', 'payment'));

    }

    public function editgateway(Request $request)
    {

        $payment = settings::where('id', $request->id)->first();


        return view('editgateway', compact('payment'));

    }

    public function updatepayment(Request $request)
    {
        $input = $request->all();


        $payment = settings::where('id', $input['id'])->first();

        $payment->value = $input['va'];
        $payment->name = $input['name'];
        $payment->save();
        $mes = "Gatepayment Update Successfully";

        return view('editgateway', compact('payment', 'mes'));

    }
}
