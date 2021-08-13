<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayController extends Controller
{
    function buyairtime(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'amount' => 'required',
            'number' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $_SERVER['HTTP_USER_AGENT'];

    }
}
