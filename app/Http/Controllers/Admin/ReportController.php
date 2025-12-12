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
            'users' => User::orderBy('name')->get(),
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
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:savings,loans,social_fund,interest',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string'
        ]);

        $data = $this->getReportData(
            $request->report_type,
            $request->start_date,
            $request->end_date,
            $request->user_id,
            $request->status
        );

        return view('admin.reports.show', compact('data'));
    }

    private function getReportData($type, $startDate, $endDate, $userId = null, $status = null)
    {
        switch ($type) {
            case 'savings':
                return $this->getSavingsReport($startDate, $endDate, $userId);
            case 'loans':
                return $this->getLoansReport($startDate, $endDate, $userId, $status);
            case 'social_fund':
                return $this->getSocialFundReport($startDate, $endDate, $userId);
            case 'interest':
                return $this->getInterestReport($startDate, $endDate, $userId);
        }
    }

    private function getSavingsReport($startDate, $endDate, $userId = null)
    {
        $query = Saving::with('user')
            ->whereBetween('payment_date', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $savings = $query->get();

        $monthlyData = $savings->groupBy(function ($item) {
            return $item->payment_date->format('Y-m');
        });

        return [
            'type' => 'savings',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'items' => $savings,
            'total' => $savings->sum('amount'),
            'stats' => [
                'average' => $savings->avg('amount') ?? 0,
                'max' => $savings->max('amount') ?? 0,
                'min' => $savings->min('amount') ?? 0,
            ],
            'monthly_labels' => $monthlyData->keys()->map(function ($month) {
                return Carbon::createFromFormat('Y-m', $month)->format('M/Y');
            })->toArray(),
            'monthly_values' => $monthlyData->map->sum('amount')->values()->toArray(),
        ];
    }

    private function getLoansReport($startDate, $endDate, $userId = null, $status = null)
    {
        $query = Loan::with('user')
            ->whereBetween('request_date', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $loans = $query->get();

        $statusData = $loans->groupBy('status');

        return [
            'type' => 'loans',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'items' => $loans,
            'total' => $loans->sum('amount'),
            'total_interest' => $loans->sum('total_interest'),
            'stats' => [
                'average' => $loans->avg('amount') ?? 0,
                'max' => $loans->max('amount') ?? 0,
                'min' => $loans->min('amount') ?? 0,
                'status_counts' => [
                    'pending' => $statusData->get('pending', collect())->count(),
                    'approved' => $statusData->get('approved', collect())->count(),
                    'rejected' => $statusData->get('rejected', collect())->count(),
                    'paid' => $statusData->get('paid', collect())->count(),
                ],
            ],
            'monthly_data' => $loans->groupBy(function ($loan) {
                return $loan->request_date->format('M/Y');
            })->map->sum('amount'),
        ];
    }

    private function getSocialFundReport($startDate, $endDate, $userId = null)
    {
        $query = SocialFund::with('user')
            ->whereBetween('payment_date', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $funds = $query->get();

        return [
            'type' => 'social_fund',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'items' => $funds,
            'total' => $funds->sum('amount'),
            'stats' => [
                'average' => $funds->avg('amount') ?? 0,
                'max' => $funds->max('amount') ?? 0,
                'min' => $funds->min('amount') ?? 0,
                'total_penalties' => $funds->sum('penalty_amount'),
                'total_late_fees' => $funds->sum('late_fee'),
            ],
            'monthly_data' => $funds->groupBy(function ($fund) {
                return $fund->payment_date->format('M/Y');
            })->map->sum('amount'),
        ];
    }

    private function getInterestReport($startDate, $endDate, $userId = null)
    {
        $query = InterestDistribution::with(['cycle', 'user'])
            ->whereBetween('distribution_date', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $distributions = $query->get();

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
                'average' => $distributions->avg('amount') ?? 0,
                'max' => $distributions->max('amount') ?? 0,
                'min' => $distributions->min('amount') ?? 0,
            ],
            'role_distributions' => $roleDistributions,
            'monthly_data' => $distributions->groupBy(function ($dist) {
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
        return $data->groupBy(function ($item) use ($dateField) {
            return $item->$dateField->format('M/Y');
        })->map->sum('amount');
    }
}