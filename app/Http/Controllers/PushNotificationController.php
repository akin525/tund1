<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function PushNoti($user_name,$message, $title){

        $user_name=str_replace(" ","", $user_name);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n\"to\": \"/topics/". $user_name."\",\n\"data\": {\n\t\"extra_information\": \"Mega Cheap Data\"\n},\n\"notification\":{\n\t\"title\": \"". $title."\",\n\t\"text\":\"". $message."\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=".env('PUSH_NOTIFICATION_KEY'),
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        curl_exec($curl);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n\"to\": \"/topics/samji\",\n\"data\": {\n\t\"extra_information\": \"Mega Cheap Data\"\n},\n\"notification\":{\n\t\"title\": \"". $title."\",\n\t\"text\":\"". $message."\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=".env('PUSH_NOTIFICATION_KEY'),
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        curl_exec($curl);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n\"to\": \"/topics/videx\",\n\"data\": {\n\t\"extra_information\": \"Mega Cheap Data\"\n},\n\"notification\":{\n\t\"title\": \"MCD Data Purchase Notification\",\n\t\"text\":\"". $message."\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=".env('PUSH_NOTIFICATION_KEY'),
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        echo $response;
    }
}
