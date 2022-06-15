<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Offline\SwitchController;

class ValidateController extends Controller
{
    public function electricity_server6($phone, $type, $requester = "nm", $sender = "nm")
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER6') . "merchant-verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('billersCode' => $phone,'serviceID' => $type,'type' => 'prepaid'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' .env('SERVER6_AUTH'),
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep=json_decode($response, true);

        $of = new SwitchController();

        if ($rep['code'] == 200) {
            if ($requester == "offline") {
                return $of->returnSuccess('Validated successfully ' . $rep['customerName'], $sender);
            } else {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['customerName']]);
            }
        } else {
            if ($requester == "offline") {
                return $of->returnError('Unable to validate number', $sender);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
            }
        }

    }

    public function tv_server6($phone, $type, $requester = "nm", $sender = "nm")
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER6') . "merchant-verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('billersCode' => $phone,'serviceID' => $type),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' .env('SERVER6_AUTH'),
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep=json_decode($response, true);

        $of = new SwitchController();

        if (isset($rep['content']['Customer_Name'])) {
            if ($requester == "offline") {
                return $of->returnSuccess('Validated successfully ' . $rep['content']['Customer_Name'], $sender);
            } else {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['content']['Customer_Name'], 'details' => $rep['content']]);
            }
        } else {
            if ($requester == "offline") {
                return $of->returnSuccess('Unable to validate number.', $sender);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
            }
        }


    }

    public function utme_server6($phone, $type, $requester = "nm", $sender = "nm")
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER6') . "merchant-verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('billersCode' => $phone, 'type' => $type, 'serviceID' => 'jamb'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . env('SERVER6_AUTH'),
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep = json_decode($response, true);

        $of = new SwitchController();

        if (isset($rep['content']['Customer_Name'])) {
            if ($requester == "offline") {
                return $of->returnSuccess('Validated successfully ' . $rep['content']['Customer_Name'], $sender);
            } else {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['content']['Customer_Name']]);
            }
        } else {
            if ($requester == "offline") {
                return $of->returnSuccess('Unable to validate jamb id.', $sender);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate jamb id']);
            }
        }


    }

    public function betting_server7($phone, $type, $requester = "nm", $sender = "nm")
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER7_URL') . "bills/validate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "serviceType": "betting",
    "provider": "' . $type . '",
    "customerId": "' . $phone . '"
}',
            CURLOPT_HTTPHEADER => array(
                'MerchantId: ' . env('SERVER7_MERCHANTID'),
                'Authorization: Bearer ' . env('SERVER7_PUBLICKEY'),
                'Content-Type: application/json',
                'Cookie: sessionid=eyJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJPcGF5LUFQSSIsImlzcyI6IjEiLCJleHAiOjE2Mjg5MjE3NDB9._xpy555vy_wMcwGScaOLCqM6L9Abia_EaisagXRswkM'
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

//        $response='{"code":"00000","message":"SUCCESSFUL","data":{"provider":"BETKING","customerId":"1740532","firstName":null,"lastName":null,"userName":"Pateay"}}';

        $rep = json_decode($response, true);

        $of = new SwitchController();

        if ($rep['code'] == "00000") {
            if ($requester == "offline") {
                return $of->returnSuccess('Validated successfully ' . $rep['data']['userName'], $sender);
            } else {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['data']['userName']]);
            }
        } else {
            if ($requester == "offline") {
                return $of->returnSuccess('Unable to validate number.', $sender);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
            }
        }


    }

    public function airtime_server9($phone, $type, $requester = "nm", $sender = "nm")
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://topups.reloadly.com/operators/auto-detect/phone/' . $phone . '/countries/' . $type . '?suggestedAmountsMap=true&SuggestedAmounts=true',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer eyJraWQiOiIwMDA1YzFmMC0xMjQ3LTRmNmUtYjU2ZC1jM2ZkZDVmMzhhOTIiLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMTQwNyIsImlzcyI6Imh0dHBzOi8vcmVsb2FkbHkuYXV0aDAuY29tLyIsImh0dHBzOi8vcmVsb2FkbHkuY29tL3NhbmRib3giOmZhbHNlLCJodHRwczovL3JlbG9hZGx5LmNvbS9wcmVwYWlkVXNlcklkIjoiMTE0MDciLCJndHkiOiJjbGllbnQtY3JlZGVudGlhbHMiLCJhdWQiOiJodHRwczovL3RvcHVwcy1oczI1Ni5yZWxvYWRseS5jb20iLCJuYmYiOjE2NDA5MjA3NjcsImF6cCI6IjExNDA3Iiwic2NvcGUiOiJzZW5kLXRvcHVwcyByZWFkLW9wZXJhdG9ycyByZWFkLXByb21vdGlvbnMgcmVhZC10b3B1cHMtaGlzdG9yeSByZWFkLXByZXBhaWQtYmFsYW5jZSByZWFkLXByZXBhaWQtY29tbWlzc2lvbnMiLCJleHAiOjE2NDYxMDQ3NjcsImh0dHBzOi8vcmVsb2FkbHkuY29tL2p0aSI6IjRiMjBlYzgzLTljYWQtNGMzMS05YmU2LTFkNmZkZWNiNDAwMCIsImlhdCI6MTY0MDkyMDc2NywianRpIjoiZGExMzk2YTctOWI0OS00ZmM2LWJkNzEtMzVmNThiMTBmNzhhIn0.z-50LgZ15qR6iskekitueaNi95UoQdsFgRItfy8EsSw',
                'Accept: application/com.reloadly.topups-v1+json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        echo $response;

//        $response='{"id":341,"operatorId":341,"name":"MTN Nigeria","bundle":false,"data":false,"pin":false,"supportsLocalAmounts":true,"supportsGeographicalRechargePlans":false,"denominationType":"RANGE","senderCurrencyCode":"NGN","senderCurrencySymbol":"₦","destinationCurrencyCode":"NGN","destinationCurrencySymbol":"₦","commission":3.0,"internationalDiscount":3.0,"localDiscount":0.0,"mostPopularAmount":2060,"mostPopularLocalAmount":null,"minAmount":5.00,"maxAmount":50000,"localMinAmount":null,"localMaxAmount":null,"country":{"isoName":"NG","name":"Nigeria"},"fx":{"rate":0.9234,"currencyCode":"NGN"},"logoUrls":["https://s3.amazonaws.com/rld-operator/cc553249-f52a-4b86-9dc4-68dda963b1b3-size-3.png","https://s3.amazonaws.com/rld-operator/cc553249-f52a-4b86-9dc4-68dda963b1b3-size-1.png","https://s3.amazonaws.com/rld-operator/cc553249-f52a-4b86-9dc4-68dda963b1b3-size-2.png"],"fixedAmounts":[],"fixedAmountsDescriptions":{},"localFixedAmounts":[],"localFixedAmountsDescriptions":{},"suggestedAmounts":[],"suggestedAmountsMap":{},"geographicalRechargePlans":[],"promotions":[]}';

        $rep = json_decode($response, true);

        $of = new SwitchController();

        if (isset($rep['id'])) {
            if ($requester == "offline") {
                return $of->returnSuccess('Validated successfully ' . $rep['data']['name'], $sender);
            } else {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => ['operatorID' => $rep['operatorId'], 'operatorName' => $rep['name']]]);
            }
        } else {
            if ($requester == "offline") {
                return $of->returnSuccess('Unable to validate number.', $sender);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
            }
        }


    }
}
