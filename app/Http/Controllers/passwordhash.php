<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class passwordhash extends Controller
{
       public function update() {
      // Validate the new password length...
      echo Hash::make("laravel"); // Hashing passwords
   }
}
