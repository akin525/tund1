<?php

namespace App\Http\Controllers\Offline;

use App\Http\Controllers\Api\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\User;
use Carbon\Carbon;

class SwitchController extends Controller
{
    public function junction($message, $sender)
    {
        echo "starting junction function <br/>";

        $input = explode(" ", $message);

        $service = $input[0];

        $pin = $input[1];

        if (!isset($service)) {
            return $this->returnError('Kindly add service to your request', $sender);
        }

        $user = User::where([["phoneno", $sender], ["pin", $pin]])->first();
        if (!$user) {
            return $this->returnError('Phone number or PIN incorrect. Kindly contact us on whatsapp@07011223737', $sender);
        }


        switch ($service) {
            case "balance":
                return $this->myBalance($sender);
            case "validate":
                $number = $input[2];
                $type = $input[3];
                $plan = $input[4];
                return $this->validateService($number, $type, $plan, $sender);
            case "airtime":
                $number = $input[2];
                $type = $input[3];
                $plan = $input[4];
                return $this->buyAirtime($number, $type, $plan, $sender);
            case "data":
                $number = $input[2];
                $type = $input[3];
                $plan = $input[4];
                return $this->buyData($number, $plan, $sender);
            case "tv":
                $number = $input[2];
                $type = $input[3];
                $plan = $input[4];
                return $this->buyTV($number, $plan, $sender);
            case "electricity":
                $number = $input[2];
                $type = $input[3];
                $plan = $input[4];
                return $this->buyElectricity($number, $type, $plan, $sender);
            case "betting":
                $number = $input[2];
                $type = $input[3];
                $plan = $input[4];
                return $this->buyBetting($number, $type, $plan, $sender);
            default:
                return $this->returnError('Invalid service provided', $sender);
        }

    }

    public function returnError($message, $sender): string
    {
        echo $message;
        return 'done';
    }

    public function returnSuccess($message, $sender): string
    {
//        echo "success: " . $message;

        $user = User::where("phoneno", $sender)->first();

        $dis = new PushNotificationController();
        return $dis->send_smsroute9ja($user->user_name, $message);

        return "done";
    }


    public function myBalance($sender)
    {
        $user = User::where("phoneno", $sender)->first();
        if (!$user) {
            return $this->returnError("Phone Number does not exist. Kindly contact us on whatsapp@07011223737", $sender);
        }

        return $this->returnSuccess("Wallet Balance: " . $user->wallet . ", Commission: " . $user->agent_commision, $sender);
    }

    public function validateService($number, $type, $plan, $sender)
    {

        $s = new ValidateController();

        switch ($type) {
            case "electricity":
                return $s->electricity_server6($number, $plan, "offline", $sender);
            case "tv":
                return $s->tv_server6($number, $plan, "offline", $sender);
            case "betting":
                return $s->betting_server7($number, strtoupper($plan), "offline", $sender);
            default:
                return $this->returnError('Invalid service provided', $sender);
        }
    }

    public function buyAirtime($number, $type, $plan, $sender)
    {
        echo "Working on buy airtime";
        $user = User::where("phoneno", $sender)->first();
        if (!$user) {
            return $this->returnError("Phone Number does not exist. Kindly contact us on whatsapp@07011223737", $sender);
        }

        $token = $user->createToken("sms")->plainTextToken;

        $ref = "MCD_SMS_" . strtoupper(substr($user->user_name, 0, 2)) . "_" . Carbon::now()->timestamp . rand();


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("APP_URL") . '/api/v2/airtime',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "provider": "' . strtoupper($type) . '",
    "amount": "' . $plan . '",
    "number": "' . $number . '",
    "country" : "Nigeria",
    "payment" : "wallet",
    "promo" : "0",
    "ref":"' . $ref . '"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'version: 6.0',
                'Content-Type: application/json',
                'User-Agent: SMS',
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        $rep = json_decode($response, true);

        if ($rep['success'] == 1) {
            $message = $rep['message'] . " with reference " . $rep['ref'] . ". Debit Amount: " . $rep['debitAmount'] . ". Commission: " . $rep['discountAmount'];
        } else {
            $message = $rep['message'];
        }
        return $this->returnSuccess($message, $sender);
    }

    public function buyData($number, $plan, $sender)
    {
        echo "Working on buy data";
        $user = User::where("phoneno", $sender)->first();
        if (!$user) {
            return $this->returnError("Phone Number does not exist. Kindly contact us on whatsapp@07011223737", $sender);
        }

        $token = $user->createToken("sms")->plainTextToken;

        $ref = "MCD_SMS_" . strtoupper(substr($user->user_name, 0, 2)) . "_" . Carbon::now()->timestamp . rand();


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("APP_URL") . '/api/v2/data',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "code": "' . $plan . '",
    "number": "' . $number . '",
    "payment" : "wallet",
    "promo" : "0",
    "ref":"' . $ref . '"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'version: 6.0',
                'Content-Type: application/json',
                'User-Agent: SMS',
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        $rep = json_decode($response, true);

        if ($rep['success'] == 1) {
            $message = $rep['message'] . " with reference " . $rep['ref'] . ". Debit Amount: " . $rep['debitAmount'] . ". Commission: " . $rep['discountAmount'];
        } else {
            $message = $rep['message'];
        }
        return $this->returnSuccess($message, $sender);
    }

    public function buyTV($number, $plan, $sender)
    {
        echo "Working on buy tv";
        $user = User::where("phoneno", $sender)->first();
        if (!$user) {
            return $this->returnError("Phone Number does not exist. Kindly contact us on whatsapp@07011223737", $sender);
        }

        $token = $user->createToken("sms")->plainTextToken;

        $ref = "MCD_SMS_" . strtoupper(substr($user->user_name, 0, 2)) . "_" . Carbon::now()->timestamp . rand();


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("APP_URL") . '/api/v2/tv',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "code": "' . $plan . '",
    "number": "' . $number . '",
    "payment" : "wallet",
    "promo" : "0",
    "ref":"' . $ref . '"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'version: 6.0',
                'Content-Type: application/json',
                'User-Agent: SMS',
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        $rep = json_decode($response, true);

        if ($rep['success'] == 1) {
            $message = $rep['message'] . " with reference " . $rep['ref'] . ". Debit Amount: " . $rep['debitAmount'] . ". Commission: " . $rep['discountAmount'];
        } else {
            $message = $rep['message'];
        }
        return $this->returnSuccess($message, $sender);
    }

    public function buyElectricity($number, $type, $plan, $sender)
    {
        echo "Working on buy electricity";
        $user = User::where("phoneno", $sender)->first();
        if (!$user) {
            return $this->returnError("Phone Number does not exist. Kindly contact us on whatsapp@07011223737", $sender);
        }

        $token = $user->createToken("sms")->plainTextToken;

        $ref = "MCD_SMS_" . strtoupper(substr($user->user_name, 0, 2)) . "_" . Carbon::now()->timestamp . rand();


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("APP_URL") . '/api/v2/electricity',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "provider": "' . $type . '",
    "amount": "' . $plan . '",
    "number": "' . $number . '",
    "payment" : "wallet",
    "promo" : "0",
    "ref":"' . $ref . '"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'version: 6.0',
                'Content-Type: application/json',
                'User-Agent: SMS',
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        $rep = json_decode($response, true);

        if ($rep['success'] == 1) {
            $message = $rep['message'] . " with reference " . $rep['ref'] . ". Debit Amount: " . $rep['debitAmount'] . ". Commission: " . $rep['discountAmount'];
        } else {
            $message = $rep['message'];
        }
        return $this->returnSuccess($message, $sender);
    }

    public function buyBetting($number, $type, $plan, $sender)
    {
        echo "Working on buy betting";
        $user = User::where("phoneno", $sender)->first();
        if (!$user) {
            return $this->returnError("Phone Number does not exist. Kindly contact us on whatsapp@07011223737", $sender);
        }

        $token = $user->createToken("sms")->plainTextToken;

        $ref = "MCD_SMS_" . strtoupper(substr($user->user_name, 0, 2)) . "_" . Carbon::now()->timestamp . rand();


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("APP_URL") . '/api/v2/betting',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "provider": "' . $type . '",
    "amount": "' . $plan . '",
    "number": "' . $number . '",
    "payment" : "wallet",
    "promo" : "0",
    "ref":"' . $ref . '"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'version: 6.0',
                'Content-Type: application/json',
                'User-Agent: SMS',
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        $rep = json_decode($response, true);

        if ($rep['success'] == 1) {
            $message = $rep['message'] . " with reference " . $rep['ref'] . ". Debit Amount: " . $rep['debitAmount'] . ". Commission: " . $rep['discountAmount'];
        } else {
            $message = $rep['message'];
        }
        return $this->returnSuccess($message, $sender);
    }
}
