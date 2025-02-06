<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Exports\SavingsReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class SavingsReportController extends Controller
{
    public function export(Request $request)
    {
        return Excel::download(new SavingsReportExport($request->start_date, $request->end_date), 'relatorio_poupancas.xlsx');
    }
}
