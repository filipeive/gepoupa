<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Saving;
use App\Models\SocialFund;
use App\Models\User;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
{
    $data = [
        'totalSavings' => Saving::sum('amount'),
        'activeLoans' => Loan::where('status', 'approved')->sum('amount'),
        'activeMembers' => User::where('status', true)->count(),
        'socialFund' => SocialFund::sum('amount'),
        //membros activos
        'activeUsers' => User::where('status', true)->count(),
        //membros inativos
        'inactiveUsers' => User::where('status', false)->count() ,
        //fondos sociais ativos
        'activeSocialFunds' => SocialFund::where('status', true)->count(),
        //fondos sociais inativos
        'inactiveSocialFunds' => SocialFund::where('status', false)->count(),
        //empréstimos ativos
        'activeLoans' => Loan::where('status', 'approved')->count(),
        //empréstimos inativos
        'inactiveLoans' => Loan::where('status', 'rejected')->orWhere('status', 'pending')->count(),
        //'recentActivities' => $this->getRecentActivities(),
        //'upcomingPayments' => $this->getUpcomingPayments(),
        //'savingsChart' => $this->getSavingsChartData(),
        //'loansChart' => $this->getLoansChartData(),
    ];
    $socialFundsData = [
        'total' => SocialFund::sum('amount'),
        'pending' => SocialFund::where('status', 'pending')->count(),
        'paid' => SocialFund::where('status', 'paid')->count(),
        'late' => SocialFund::where('status', 'late')->count(),
        'penalty_total' => SocialFund::sum('penalty_amount')
    ];

    return view('admin.dashboard', compact('data', 'socialFundsData'));
}
}
 