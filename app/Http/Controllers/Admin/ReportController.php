<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function memberReport(User $user)
    {
        $totalSavings = $user->savings()->sum('amount');
        $totalLoans = $user->loans()->sum('amount');
        $totalInterest = $user->interestEarnings()->sum('amount');
        $socialFund = $user->socialFundContributions()->sum('amount');
        
        $savings = $user->savings()->latest()->get();
        $loans = $user->loans()->latest()->get();
        
        return view('admin.reports.member-report', compact(
            'user',
            'totalSavings',
            'totalLoans',
            'totalInterest',
            'socialFund',
            'savings',
            'loans'
        ));
    }
}
