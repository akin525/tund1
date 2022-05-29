<?php

namespace App\Http\Controllers;

use App\Models\airtimeserver;
use App\Models\AppAirtimeControl;
use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\dataserver;
use App\Models\ResellerElecticity;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServerController
{
    public function airtime(Request $request)
    {
        $data = AppAirtimeControl::get();

        return view('airtimecontrol', compact('data'));
    }

    public function airtimeEdit($id)
    {
        $data = AppAirtimeControl::find($id);

        if(!$data){
            return redirect()->route('airtimecontrol')->with('error', 'Network does not exist');
        }

        return view('airtimecontrol_edit', compact('data'));
    }

    public function airtimeUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'discount'      => 'required',
            'status' => 'required',
            'server' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data = AppDataControl::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }
        $data->discount = $input['discount'];
        $data->status = $input['status'];
        $data->server = $input['server'];
        $data->save();

        return redirect()->route('airtimecontrol')->with('success', $data->network . ' has been updated successfully');
    }

    public function changeserver(Request $request)
    {
        $airtime = airtimeserver::where('name', 'airtime')->first();


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

    public function dataserveedit($id)
    {
        $data = dataserver::find($id);

        if(!$data){
            return redirect()->route('datacontrol')->with('error', 'Plan does not exist');
        }

        return view('datacontrol_edit', compact('data'));
    }

    public function dataserveUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'product_name'      => 'required',
            'provider_price' => 'required',
            'amount' => 'required',
            'status' => 'required',
            'server' => 'required',
            'note' => 'nullable'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data = AppDataControl::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }
        $data->name = $input['product_name'];
        $data->price = $input['provider_price'];
        $data->pricing = $input['amount'];
        $data->status = $input['status'];
        $data->server = $input['server'];
        $data->note = $input['note'];
        $data->save();

        return redirect()->route('dataplans')->with('success', $data->name . ' has been updated successfully');
    }


    public function tvserver()
    {
        $data = AppCableTVControl::paginate(10);

        return view('tvcontrol', compact('data'));
    }

    public function tvEdit($id)
    {
        $data = AppCableTVControl::find($id);

        if(!$data){
            return redirect()->route('tvcontrol')->with('error', 'Network does not exist');
        }

        return view('tvcontrol_edit', compact('data'));
    }

    public function tvUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'name'      => 'required',
            'price' => 'required',
            'discount' => 'required',
            'status' => 'required',
            'server' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data = AppCableTVControl::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }
        $data->name = $input['name'];
        $data->price = $input['price'];
        $data->status = $input['status'];
        $data->server = $input['server'];
        $data->discount = $input['discount'];
        $data->save();

        return redirect()->route('tvcontrol')->with('success', $data->name . ' has been updated successfully');
    }


    public function electricityserver()
    {
        $data = ResellerElecticity::get();

        return view('electricitycontrol', compact('data'));
    }

    public function electricityEdit($id)
    {
        $data = ResellerElecticity::find($id);

        if(!$data){
            return redirect()->route('electricitycontrol')->with('error', 'Electricity does not exist');
        }

        return view('electricitycontrol_edit', compact('data'));
    }

    public function electricityUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'discount' => 'required',
            'status' => 'required',
            'server' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data = AppCableTVControl::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }

        $data->status = $input['status'];
        $data->server = $input['server'];
        $data->discount = $input['discount'];
        $data->save();

        return redirect()->route('electricitycontrol')->with('success', $data->name . ' has been updated successfully');
    }

    public function userole(Request $request)
    {
        $user = user::paginate(50);

        return view('role', compact('user'));

    }

    public function updateuserole(Request $request)
    {

        $role = user::where('id', $request->id)->first();

        $role->status = $request->status;
        $role->save();

        return redirect('/role')->with('success', $role->user_name . " role has been change to " . $request->status);


    }
}
