<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterestRates;
use App\Exports\InterestRateReport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Loan;
use Carbon\Carbon;
use App\Models\SavingCycle; // Corrija aqui!
use App\Models\InterestDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterestRatesController extends Controller
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
        $interestRate = new InterestRates();
        $interestRate->rate = $request->input('rate');
        $interestRate->effective_date = $request->input('effective_date');
        $interestRate->description = $request->input('description');
        $interestRate->save();

        return redirect()->back()->with('success', 'Taxa de Juros definida com sucesso!');
    }

    public function calculateDistribution()
    {
        $undistributedInterest = DB::table('loan_payments')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('interest_distributions')
                    ->whereRaw('interest_distributions.cycle_id = loan_payments.cycle_id'); // Correção aqui
            })
            ->sum('interest_amount');



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

        // Buscar ciclos ativos
        $cycles = SavingCycle::where('status', 'active')
            ->orWhere('status', 'completed')
            ->orderBy('start_date', 'desc')
            ->get();


        // Verificar se os ciclos foram carregados corretamente
        if ($cycles->isEmpty()) {
            dd('Nenhum ciclo encontrado!', $cycles);
        }

        return view('admin.interest-rates.calculate', compact(
            'undistributedInterest',
            'members',
            'totalSavings',
            'cycles' // Certifica-te de que esta variável está aqui
        ));
    }

    public function distribute(Request $request)
    {
        $validated = $request->validate([
            'distribution_date' => 'required|date',
            'description' => 'required|string',
            'distributions' => 'required|array',
            'distributions.*.user_id' => 'required|exists:users,id',
            'distributions.*.amount' => 'required|numeric|min:0',
            'cycle_id' => 'required|exists:saving_cycles,id' // Adicione validação para cycle_id
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['distributions'] as $distribution) {
                InterestDistribution::create([
                    'user_id' => $distribution['user_id'],
                    'amount' => $distribution['amount'],
                    'distribution_date' => $validated['distribution_date'],
                    'description' => $validated['description'],
                    'cycle_id' => $validated['cycle_id'] // Adicione o cycle_id
                ]);
            }
        });

        return redirect()
            ->route('interest-rates.index')
            ->with('success', 'Juros distribuídos com sucesso!');
    }

    public function report(Request $request)
    {
        // Converter as datas de string para objetos Carbon
        $startDate = $request->has('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->startOfMonth();

        $endDate = $request->has('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now()->endOfMonth();

        // Consulta para relatório de juros
        $interestReport = DB::table('loan_payments')
            ->whereBetween('payment_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->select(
                DB::raw('SUM(interest_amount) as total_interest'),
                DB::raw('COUNT(DISTINCT loan_id) as total_loans'),
                DB::raw('AVG(interest_amount) as average_interest')
            )
            ->first();

        // Consulta para distribuição
        $distributionReport = InterestDistribution::whereBetween('distribution_date', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        ])
            ->with('user')
            ->get()
            ->groupBy('user_id');

        // Passar as datas como objetos Carbon para a view
        return view('admin.interest-rates.report', compact(
            'interestReport',
            'distributionReport',
            'startDate',
            'endDate'
        ));
    }

    public function export()
    {
        return Excel::download(new InterestRateReport, 'taxas-juros.xlsx');
    }
}
