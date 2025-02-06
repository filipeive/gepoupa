<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterestRates;
use App\Models\Loan;
use App\Models\InterestDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterestManagementController extends Controller
{
    public function index()
    {
        $interestRates = InterestRates::latest()->get(); // Agora funciona corretamente
        $totalInterestCollected = DB::table('loan_payments')
            ->sum('interest_amount');
        
        $monthlyInterest = DB::table('loan_payments')
            ->select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(interest_amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $distributions = InterestDistribution::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.interest-rates.index', compact(
            'interestRates',
            'totalInterestCollected',
            'monthlyInterest',
            'distributions'
        ));
    }

    public function setRate(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'rate' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        // Definir a nova taxa de juros
        $interestRate = new InterestRate();
        $interestRate->rate = $request->input('rate');
        $interestRate->effective_date = $request->input('effective_date');
        $interestRate->description = $request->input('description');
        $interestRate->save();

        return redirect()->back()->with('success', 'Taxa de Juros definida com sucesso!');
    }
    public function calculateDistribution()
    {
        // Calcular juros não distribuídos
        $undistributedInterest = DB::table('loan_payments')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('interest_distributions')
                    ->whereRaw('interest_distributions.payment_id = loan_payments.id');
            })
            ->sum('interest_amount');

        // Buscar membros ativos com poupanças
        $members = DB::table('users')
            ->join('savings', 'users.id', '=', 'savings.user_id')
            ->where('users.status', true)
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(savings.amount) as total_savings')
            )
            ->groupBy('users.id', 'users.name')
            ->get();

        $totalSavings = $members->sum('total_savings');

        return view('admin.interest-rates.calculate', compact(
            'undistributedInterest',
            'members',
            'totalSavings'
        ));
    }

    public function distribute(Request $request)
    {
        $validated = $request->validate([
            'distribution_date' => 'required|date',
            'description' => 'required|string',
            'distributions' => 'required|array',
            'distributions.*.user_id' => 'required|exists:users,id',
            'distributions.*.amount' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['distributions'] as $distribution) {
                InterestDistribution::create([
                    'user_id' => $distribution['user_id'],
                    'amount' => $distribution['amount'],
                    'distribution_date' => $validated['distribution_date'],
                    'description' => $validated['description']
                ]);
            }
        });

        return redirect()
            ->route('interest-management.index')
            ->with('success', 'Juros distribuídos com sucesso!');
    }

    public function report(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $interestReport = DB::table('loan_payments')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(interest_amount) as total_interest'),
                DB::raw('COUNT(DISTINCT loan_id) as total_loans'),
                DB::raw('AVG(interest_amount) as average_interest')
            )
            ->first();

        $distributionReport = InterestDistribution::whereBetween('distribution_date', [$startDate, $endDate])
            ->with('user')
            ->get()
            ->groupBy('user_id');

        return view('admin.interest-management.report', compact(
            'interestReport',
            'distributionReport',
            'startDate',
            'endDate'
        ));
    }
}