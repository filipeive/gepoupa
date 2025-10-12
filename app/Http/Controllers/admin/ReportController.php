<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Saving, Loan, SocialFund, InterestDistribution};
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $data = [
            'totalSavings' => Saving::sum('amount'),
            'totalLoans' => Loan::sum('amount'),
            'totalSocialFund' => SocialFund::sum('amount'),
            'totalInterestDistributed' => InterestDistribution::sum('amount'),
            'monthlyStats' => $this->getMonthlyStats(),
            'yearlyStats' => $this->getYearlyStats(),
            'loanStats' => $this->getLoanStats(),
            'savingsStats' => $this->getSavingsStats(),
        ];

        return view('admin.reports.index', $data);
    }

    private function getMonthlyStats()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return [
            'savings' => Saving::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->sum('amount'),
            'loans' => Loan::whereBetween('request_date', [$startOfMonth, $endOfMonth])->sum('amount'),
            'socialFund' => SocialFund::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->sum('amount'),
        ];
    }

    private function getYearlyStats()
    {
        return DB::table('savings')
            ->select(DB::raw('MONTH(payment_date) as month'), DB::raw('SUM(amount) as total'))
            ->whereYear('payment_date', date('Y'))
            ->groupBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
    }

    private function getLoanStats()
    {
        return [
            'pending' => Loan::where('status', 'pending')->count(),
            'approved' => Loan::where('status', 'approved')->count(),
            'paid' => Loan::where('status', 'paid')->count(),
            'totalAmount' => Loan::sum('amount'),
            'totalInterest' => Loan::sum('total_interest'),
        ];
    }

    private function getSavingsStats()
    {
        return DB::table('savings')
            ->join('users', 'savings.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('SUM(savings.amount) as total'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'report_type' => 'required|in:savings,loans,social_fund,interest'
        ]);

        $data = $this->getReportData(
            $request->report_type,
            $request->start_date,
            $request->end_date
        );

        return view('admin.reports.show', compact('data'));
    }

    private function getReportData($type, $startDate, $endDate)
    {
        switch ($type) {
            case 'savings':
                return $this->getSavingsReport($startDate, $endDate);
            case 'loans':
                return $this->getLoansReport($startDate, $endDate);
            case 'social_fund':
                return $this->getSocialFundReport($startDate, $endDate);
            case 'interest':
                return $this->getInterestReport($startDate, $endDate);
        }
    }
    private function getSavingsReport($startDate, $endDate)
    {
        $savings = Saving::with('user')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $monthlyData = $savings->groupBy(function($item) {
            return $item->payment_date->format('Y-m');
        });

        return [
            'type' => 'savings',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'items' => $savings,
            'total' => $savings->sum('amount'),
            'stats' => [
                'average' => $savings->avg('amount'),
                'max' => $savings->max('amount'),
                'min' => $savings->min('amount'),
            ],
            'monthly_labels' => $monthlyData->keys()->map(function($month) {
                return Carbon::createFromFormat('Y-m', $month)->format('M/Y');
            })->toArray(),
            'monthly_values' => $monthlyData->map->sum('amount')->values()->toArray(),
        ];
    }

    private function getLoansReport($startDate, $endDate)
    {
        $loans = Loan::with('user')
            ->whereBetween('request_date', [$startDate, $endDate])
            ->get();

        $statusData = $loans->groupBy('status');

        return [
            'type' => 'loans',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'items' => $loans,
            'total' => $loans->sum('amount'),
            'total_interest' => $loans->sum('total_interest'),
            'stats' => [
                'average' => $loans->avg('amount'),
                'max' => $loans->max('amount'),
                'min' => $loans->min('amount'),
                'status_counts' => [
                    'pending' => $statusData->get('pending', collect())->count(),
                    'approved' => $statusData->get('approved', collect())->count(),
                    'rejected' => $statusData->get('rejected', collect())->count(),
                    'paid' => $statusData->get('paid', collect())->count(),
                ],
            ],
            'monthly_data' => $loans->groupBy(function($loan) {
                return $loan->request_date->format('M/Y');
            })->map->sum('amount'),
        ];
    }

    private function getSocialFundReport($startDate, $endDate)
    {
        $funds = SocialFund::with('user')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        return [
            'type' => 'social_fund',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'items' => $funds,
            'total' => $funds->sum('amount'),
            'stats' => [
                'average' => $funds->avg('amount'),
                'max' => $funds->max('amount'),
                'min' => $funds->min('amount'),
                'total_penalties' => $funds->sum('penalty_amount'),
                'total_late_fees' => $funds->sum('late_fee'),
            ],
            'monthly_data' => $funds->groupBy(function($fund) {
                return $fund->payment_date->format('M/Y');
            })->map->sum('amount'),
        ];
    }

    private function getInterestReport($startDate, $endDate)
    {
        $distributions = InterestDistribution::with(['cycle', 'user'])
            ->whereBetween('distribution_date', [$startDate, $endDate])
            ->get();

        $roleDistributions = DB::table('interest_distribution_roles')
            ->select('role', DB::raw('SUM(amount) as total_amount'))
            ->whereBetween('distribution_date', [$startDate, $endDate])
            ->groupBy('role')
            ->get();

        return [
            'type' => 'interest',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'items' => $distributions,
            'total' => $distributions->sum('amount'),
            'stats' => [
                'average' => $distributions->avg('amount'),
                'max' => $distributions->max('amount'),
                'min' => $distributions->min('amount'),
            ],
            'role_distributions' => $roleDistributions,
            'monthly_data' => $distributions->groupBy(function($dist) {
                return $dist->distribution_date->format('M/Y');
            })->map->sum('amount'),
        ];
    }
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
    // Método auxiliar para formatar dados para gráficos
    private function formatChartData($data, $dateField = 'created_at')
    {
        return $data->groupBy(function($item) use ($dateField) {
            return $item->$dateField->format('M/Y');
        })->map->sum('amount');
    }
}