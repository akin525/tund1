<?php

namespace App\Http\Controllers;

use App\Model\PndL;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    function pnl(){
        $data['income']=PndL::where([['date', 'LIKE','%'. Carbon::now()->format("y-m").'%'], ['type','income']])->get();
        $data['expenses']=PndL::where([['date', 'LIKE','%'. Carbon::now()->format("y-m").'%'], ['type','expenses']])->get();
        $data['ti']=0;
        $data['te']=0;

        return view('report_pnl', $data);
    }
}
