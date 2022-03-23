<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\ValidateController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'service' => 'required',
            'provider' => 'required',
            'number' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $s = new ValidateController();

        switch ($input['service']) {
            case "electricity":
                return $s->electricity_server6($input['number'], strtolower($input['provider']));
            case "tv":
                return $s->tv_server6($input['number'], strtolower($input['provider']));
            case "betting":
                return $s->betting_server7($input['number'], strtoupper($input['provider']));
            case "smile":
                return $s->tv_server6($input['number'], strtolower($input['provider']));
            case "airtime":
                return $s->airtime_server9($input['number'], strtolower($input['provider']));
            case "jamb":
                return $s->utme_server6($input['number'], strtolower($input['provider']));
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }
    }
}
