<?php

namespace App\Http\Controllers;

use App\Models\airtimeserver;
use App\Models\dataserver;
use Illuminate\Http\Request;

class ServerController
{
    public function airtime(Request $request)
    {
        $airtime = airtimeserver::get();

        return view('servercontrol', $airtime);

    }

    public function changeserver(Request $request)
    {
        $airtime = airtimeserver::where('name', 'airtime')->first();
//return $airtime;
        if ($request->network == "mtn") {

            $airtime->mtn = $request->number;
            $airtime->save();
        }
        if ($request->network == "glo") {

            $airtime->glo = $request->number;
            $airtime->save();
        }
        if ($request->network == "airtel") {

            $airtime->airtel = $request->number;
            $airtime->save();
        }
        if ($request->network == "etisalat") {

            $airtime->etisalat = $request->number;
            $airtime->save();
        }
        $success = $request->network . " Server Change To Server " . $request->number;

        return view('servercontrol', compact('airtime', 'success'));


    }

    public function dataserve2()
    {
        $data = dataserver::paginate(10);

        return view('datacontrol', compact('data'));
    }

    public function updatedataserve(Request $request)
    {
        $data = dataserver::where('id', $request->id)->first();
        $data->server = $request->number;
        $data->save();

        return redirect('/datacontrol')->with('success', $data->name . ' server change successful');


    }
}
