<?php

namespace App\Http\Controllers;

use App\Models\PndL;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    function pnl(){
        $data['income']=PndL::where([['date', 'LIKE','%'. Carbon::now()->format("y-m").'%'], ['type','income']])->get();
        $data['incomed']=PndL::where([['date', 'LIKE','%'. Carbon::now()->format("y-m").'%'], ['type','income']])->distinct('gl')->select('gl')->get();
        $data['income_sum']=PndL::where([['date', 'LIKE','%'. Carbon::now()->format("y-m").'%'], ['type','income']])->sum('amount');
        $data['expenses']=PndL::where([['date', 'LIKE','%'. Carbon::now()->format("y-m").'%'], ['type','expenses']])->get();
        $data['expensed']=PndL::where([['date', 'LIKE','%'. Carbon::now()->format("y-m").'%'], ['type','expenses']])->distinct('gl')->select('gl')->get();
        $data['expense_sum']=PndL::where([['date', 'LIKE','%'. Carbon::now()->format("y-m").'%'], ['type','expenses']])->sum('amount');
        $data['ti']=0;
        $data['te']=0;

        return view('report_pnl', $data);
    }
}
