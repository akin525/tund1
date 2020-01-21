<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\View;

class UsersController extends Controller
{
    public function index(Request $request){

    	 $users = DB::table('tbl_agents')->orderBy('id', 'desc')->paginate(500);

        return view('users', ['users' => $users]);

    }
}
