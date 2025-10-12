<?php
namespace App\Http\Controllers\member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\SocialFund;

class MemberDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = auth()->user();

        $totalSavings = Saving::where('user_id', $user->id)->sum('amount');
        $activeLoans = Loan::where('user_id', $user->id)
            ->where('status', 'approved')
            ->get();
        $socialFunds = SocialFund::where('user_id', $user->id)
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        return view('member.dashboard', compact(
            'totalSavings',
            'activeLoans',
            'socialFunds'
        ));
    }
}