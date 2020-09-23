<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RaveHookController extends Controller
{
    public function index(){
        // Retrieve the request's body
        $body = @file_get_contents("php://input");

// retrieve the signature sent in the reques header's.
        $signature = (isset($_SERVER['HTTP_VERIF_HASH']) ? $_SERVER['HTTP_VERIF_HASH'] : '');

        /* It is a good idea to log all events received. Add code *
         * here to log the signature and body to db or file       */

        if (!$signature) {
            // only a post with Flutterwave signature header gets our attention
            exit();
        }

// Store the same signature on your server as an env variable and check against what was sent in the headers
        $local_signature = getenv('SECRET_HASH');

// confirm the event's signature
        if( $signature !== $local_signature ){
            // silently forget this ever happened
            exit();
        }

        http_response_code(200); // PHP 5.4 or greater
// parse event (which is json string) as object
// Give value to your customer but don't give any output
// Remember that this is a call from rave's servers and
// Your customer is not seeing the response here at all
        $response = json_decode($body);
        if ($response->status == 'successful') {
            # code...
            // TIP: you may still verify the transaction
            // before giving value.
        }
        exit();
    }

}

/*
{
    "event": "charge.completed",
  "data": {
    "id": 285959875,
    "tx_ref": "Links-616626414629",
    "flw_ref": "PeterEkene/FLW270177170",
    "device_fingerprint": "a42937f4a73ce8bb8b8df14e63a2df31",
    "amount": 100,
    "currency": "NGN",
    "charged_amount": 100,
    "app_fee": 1.4,
    "merchant_fee": 0,
    "processor_response": "Approved by Financial Institution",
    "auth_model": "PIN",
    "ip": "197.210.64.96",
    "narration": "CARD Transaction ",
    "status": "successful",
    "payment_type": "card",
    "created_at": "2020-07-06T19:17:04.000Z",
    "account_id": 17321,
    "customer": {
        "id": 215604089,
      "name": "Yemi Desola",
      "phone_number": null,
      "email": "user@gmail.com",
      "created_at": "2020-07-06T19:17:04.000Z"
    },
    "card": {
        "first_6digits": "123456",
      "last_4digits": "7889",
      "issuer": "VERVE FIRST CITY MONUMENT BANK PLC",
      "country": "NG",
      "type": "VERVE",
      "expiry": "02/23"
    }
  }*/
