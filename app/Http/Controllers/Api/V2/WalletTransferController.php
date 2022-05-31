<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user=User::where("user_name",$input['user_name'])->orwhere("email",$input['user_name'])->first();

        if(!$user){
            return response()->json(['success' => 0, 'message' => 'Invalid username']);
        }


        return response()->json(['success' => 1, 'message' => 'Validated Successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'amount' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user=User::where("user_name",$input['user_name'])->orwhere("email",$input['user_name'])->first();

        if(!$user){
            return response()->json(['success' => 0, 'message' => 'Invalid username']);
        }

        if($user->wallet < 1){
            return response()->json(['success' => 0, 'message' => 'Insufficient fund']);
        }

        $amount=$input['amount'];

        if($amount < 1){
            return response()->json(['success' => 0, 'message' => 'Invalid amount']);
        }

        if($input['amount'] > $user->wallet){
            return response()->json(['success' => 0, 'message' => 'Insufficient fund ']);
        }

        $user=User::find(Auth::id());
        $user->wallet-=$amount;


        return response()->json(['success' => 1, 'message' => 'Validated Successfully']);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
