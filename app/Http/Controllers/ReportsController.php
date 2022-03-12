<?php

namespace App\Http\Controllers;

use App\Models\PndL;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    function pnl(Request $request)
    {
        if (!isset($request->date)) {
            $date = Carbon::now()->format("Y-m");
        } else {
            $date = Carbon::parse($request->date)->format("Y-m");
        }
        $data['income'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'income']])->get();
        $data['incomed'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'income']])->distinct('gl')->select('gl')->get();
        $data['income_sum'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'income']])->sum('amount');
        $data['expenses'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->get();
        $data['expensed'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->distinct('gl')->select('gl')->get();
        $data['expense_sum'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->sum('amount');
        $data['ti'] = 0;
        $data['te'] = 0;
        $data['date'] = $date;

        return view('report_pnl', $data);
    }
}
