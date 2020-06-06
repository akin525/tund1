<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\logvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UltilityController extends Controller
{
    public function mcd_logvoice(Request $request){

        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'name' => 'required',
            'voice' => 'required',
            'page' => 'required',
            'code' => 'required',
            'version' => 'required',
            'device_details' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            try {
               logvoice::create($input);
                return response()->json(['status'=> 1, 'message'=>'Voice logged Successfully']);
            }catch(\Exception $e){
                return response()->json(['status'=> 0, 'message'=>'Error logging voice','error' => $e]);
            }
        }else{
            return response()->json(['status'=> 0, 'message'=>'Error logging voice', 'error' => $validator->errors()]);
        }

    }

    function hook(Request $request){
        $input = $request->all();

//        $data1= implode($input);
        $data2= json_encode($input);

        DB::table('test')->insert(['name'=> 'webhook', 'request'=>$request, 'data2'=>$data2]);

        echo "success";
    }
}
