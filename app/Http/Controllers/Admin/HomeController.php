<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Saving;
use App\Models\SocialFund;
use App\Models\User;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Dados Gerais
        $data = [
            'totalSavings' => Saving::sum('amount'),
            'activeLoans' => Loan::where('status', 'approved')->sum('amount'),
            'activeMembers' => User::where('status', true)->count(),
            'socialFund' => SocialFund::sum('amount'),
            'activeUsers' => User::where('status', true)->count(),
            'inactiveUsers' => User::where('status', false)->count(),
            'activeSocialFunds' => SocialFund::where('status', 'paid')->count(),
            'inactiveSocialFunds' => SocialFund::where('status', 'pending')->count(),
            'activeLoansCount' => Loan::where('status', 'approved')->count(),
            'inactiveLoansCount' => Loan::where('status', 'rejected')->orWhere('status', 'pending')->count(),
        ];

        // Dados do Fundo Social
        $socialFundsData = [
            'total' => SocialFund::sum('amount'),
            'pending' => SocialFund::where('status', 'pending')->count(),
            'paid' => SocialFund::where('status', 'paid')->count(),
            'late' => SocialFund::where('status', 'late')->count(),
            'penalty_total' => SocialFund::sum('penalty_amount'),
        ];

        // Atividades Recentes
        $recentActivities = $this->getRecentActivities();

        // Próximos Vencimentos
        $upcomingPayments = $this->getUpcomingPayments();

        // Dados para Gráficos
        $savingsChartData = $this->getSavingsChartData();
        $loansChartData = $this->getLoansChartData();

        return view('admin.dashboard', compact(
            'data',
            'socialFundsData',
            'recentActivities',
            'upcomingPayments',
            'savingsChartData',
            'loansChartData'
        ));
    }

    private function getRecentActivities()
    {
        $savings = Saving::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($saving) {
                return [
                    'type' => 'Poupança',
                    'user' => $saving->user->name,
                    'amount' => $saving->amount,
                    'date' => $saving->payment_date,
                ];
            });

        $loans = Loan::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($loan) {
                return [
                    'type' => 'Empréstimo',
                    'user' => $loan->user->name,
                    'amount' => $loan->amount,
                    'date' => $loan->request_date,
                ];
            });

        $socialFunds = SocialFund::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($socialFund) {
                return [
                    'type' => 'Fundo Social',
                    'user' => $socialFund->user->name,
                    'amount' => $socialFund->amount,
                    'date' => $socialFund->payment_date,
                ];
            });

        return $savings->merge($loans)->merge($socialFunds)->sortByDesc('date')->take(5);
    }

   /*  private function getUpcomingPayments()
    {
        $loans = Loan::with('user')
            ->where('status', 'approved')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(function ($loan) {
                return [
                    'type' => 'Empréstimo',
                    'user' => $loan->user->name,
                    'amount' => $loan->amount,
                    'due_date' => $loan->due_date,
                ];
            });

        $socialFunds = SocialFund::with('user')
            ->where('status', 'pending')
            ->where('payment_date', '>=', now())
            ->orderBy('payment_date')
            ->limit(5)
            ->get()
            ->map(function ($socialFund) {
                return [
                    'type' => 'Fundo Social',
                    'user' => $socialFund->user->name,
                    'amount' => $socialFund->amount,
                    'due_date' => $socialFund->payment_date,
                ];
            });

        return $loans->merge($socialFunds)->sortBy('due_date')->take(5);
    } */
    private function getUpcomingPayments()
    {
        // Carregar empréstimos aprovados com a relação de usuários
        $loans = Loan::with('user')
            ->where('status', 'approved')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();  // Retorna como coleção de objetos Eloquent

        // Carregar fundos sociais pendentes com a relação de usuários
        $socialFunds = SocialFund::with('user')
            ->where('status', 'pending')
            ->where('payment_date', '>=', now())
            ->orderBy('payment_date')
            ->limit(5)
            ->get();  // Retorna como coleção de objetos Eloquent

        // Mesclar e ordenar os dois conjuntos de dados, mantendo os objetos Eloquent
        $combined = $loans->merge($socialFunds)
            ->sortBy('due_date')  // ou 'payment_date', dependendo de qual data você prefere
            ->take(5);  // Limita a 5 itens

        // Mapear os dados para exibição ou outros usos
        return $combined->map(function ($item) {
            return [
                'type' => $item instanceof Loan ? 'Empréstimo' : 'Fundo Social',
                'user' => $item->user->name,
                'amount' => $item->amount,
                'due_date' => $item->due_date,  // ou $item->payment_date, dependendo do tipo
            ];
        });
    }


    private function getSavingsChartData()
    {
        $savings = Saving::selectRaw('YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $savings->map(function ($saving) {
            return Carbon::create($saving->year, $saving->month)->format('M/Y');
        });

        $data = $savings->pluck('total');

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getLoansChartData()
    {
        $loans = Loan::select('status', \DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        $labels = $loans->pluck('status');
        $data = $loans->pluck('total');

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}