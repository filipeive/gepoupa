<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
//db
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MembersExport;

class MemberManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $members = User::where('role', 'member')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(10);

        return view('admin.members.index', compact('members', 'search'));
    }

    public function show(User $member)
    {
        // Cálculos financeiros
        $totalSavings = $member->savings()->sum('amount');
        $currentYearSavings = $member->savings()
            ->whereYear('payment_date', date('Y'))
            ->sum('amount');

        $totalSocialFunds = $member->socialFunds()->sum('amount');
        $pendingSocialFunds = $member->socialFunds()
            ->where('status', 'pending')
            ->sum('amount');

        // Empréstimos ativos (aprovados e não pagos)
        $activeLoans = $member->loans()
            ->where('status', 'approved')
            ->get();

        // Todos os empréstimos
        $loans = $member->loans()->orderBy('request_date', 'desc')->get();

        // Cálculo de juros pagos
        $totalInterestPaid = $member->loans()
            ->join('loan_payments', 'loans.id', '=', 'loan_payments.loan_id')
            ->sum('loan_payments.interest_amount');

        // Cálculo de empréstimos totais
        $loanStats = [
            'total' => $member->loans()->count(),
            'active' => $member->loans()->where('status', 'approved')->count(),
            'paid' => $member->loans()->where('status', 'paid')->count(),
            'pending' => $member->loans()->where('status', 'pending')->count(),
        ];

        // Cálculo de pagamentos de empréstimos
        $loanPayments = $member->loans()
            ->join('loan_payments', 'loans.id', '=', 'loan_payments.loan_id')
            ->select(
                'loans.id',
                'loans.amount as loan_amount',
                'loans.interest_rate',
                DB::raw('SUM(loan_payments.amount) as total_paid'),
                DB::raw('SUM(loan_payments.interest_amount) as total_interest_paid')
            )
            ->groupBy('loans.id', 'loans.amount', 'loans.interest_rate')
            ->get();

        $projectedEarnings = $totalSavings * 0.15; // 15% de retorno projetado

        return view('admin.members.show', compact(
            'member',
            'totalSavings',
            'currentYearSavings',
            'totalSocialFunds',
            'pendingSocialFunds',
            'activeLoans',
            'loans',
            'totalInterestPaid',
            'projectedEarnings',
            'loanStats',
            'loanPayments'
        ));
    }
    public function export($format)
    {
        $members = User::with(['savings', 'socialFunds', 'loans'])
            ->where('role', 'member')
            ->get();

        if ($format === 'pdf') {
            $pdf = PDF::loadView('admin.members.export-pdf', compact('members'));
            return $pdf->download('members-report.pdf');
        }

        if ($format === 'excel') {
            return Excel::download(new MembersExport($members), 'members-report.xlsx');
        }
    }


}