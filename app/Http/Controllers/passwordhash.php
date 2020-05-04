<?php

namespace App\Http\Controllers;

use App\Model\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;

class passwordhash extends Controller
{
       public function update() {
      // Validate the new password length...
      echo Hash::make("laravel"); // Hashing passwords

           Transaction::where('status', '=', 'Delivered')->orWhere('status', '=', 'ORDER_RECEIVED')->orWhere('status', '=', 'ORDER_COMPLETED')->update(['status'=> 'delivered']);
           echo 'done delivered';

           Transaction::where('status', '=', 'Not Delivered')->orWhere('status', '=', 'ORDER_CANCELLED')->orWhere('status', '=', 'Unsuccessful')->orWhere('status', '=', 'Connection Error!!')->update(['status'=> 'not_delivered']);
           echo 'done not_delivered';

           Transaction::where('status', '=', 'Successful')->orWhere('status', '=', 'uccessful')->update(['status'=> 'successful']);
           echo 'done successful';

           Transaction::where('status', '=', 'Error')->update(['status'=> 'error']);
           echo 'done error';

           Transaction::where('status', '=', 'Refund')->orWhere('status', '=', 'refund')->update(['status'=> 'refunded']);
           echo 'done refund';

       }
}
