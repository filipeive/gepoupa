<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterestDistribution;
use App\Models\InterestDistributionRole;
use App\Exports\InterestDistributionsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SavingCycle;
use App\Models\User;
use App\Models\LoanPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterestDistributionController extends Controller
{
    public function index()
    {
        // Buscar distribuições com relacionamentos
        $distributions = InterestDistribution::with(['cycle', 'user'])
            ->orderBy('distribution_date', 'desc')
            ->paginate(10);
        
        // Estatísticas para o dashboard
        $statistics = [
            'totalDistributed' => InterestDistribution::sum('amount'),
            'totalPending' => LoanPayment::whereNull('distributed_at')->sum('interest_amount'),
            'totalPayments' => LoanPayment::count(),
            'distributedPayments' => LoanPayment::whereNotNull('distributed_at')->count()
        ];
        
        $roleDistributions = InterestDistributionRole::all();
        
        return view('admin.interest-distributions.index', compact(
            'distributions', 
            'roleDistributions',
            'statistics'
        ));
    }

    public function calculateDistribution()
    {
        // Buscar pagamentos não distribuídos com relacionamentos
        $pendingPayments = LoanPayment::with(['loan.user'])
            ->whereNull('distributed_at')
            ->orderBy('payment_date')
            ->get();

        // Calcular total de juros pendentes
        $totalPendingInterest = $pendingPayments->sum('interest_amount');

        // Buscar ciclo ativo
        $activeCycle = SavingCycle::where('status', 'active')->first();

        // Buscar membros com poupanças
        $membersWithSavings = DB::table('users')
            ->join('savings', 'users.id', '=', 'savings.user_id')
            ->where('users.status', true)
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(savings.amount) as total_savings')
            )
            ->groupBy('users.id', 'users.name')
            ->having('total_savings', '>', 0)
            ->get();

        $totalSavings = $membersWithSavings->sum('total_savings');

        // Calcular distribuição proposta
        $proposedDistributions = $membersWithSavings->map(function ($member) use ($totalSavings, $totalPendingInterest) {
            $percentage = $totalSavings > 0 ? ($member->total_savings / $totalSavings) * 100 : 0;
            return [
                'user_id' => $member->id,
                'name' => $member->name,
                'savings' => $member->total_savings,
                'percentage' => round($percentage, 2),
                'amount_to_receive' => round($totalPendingInterest * ($percentage / 100), 2)
            ];
        });

        return view('admin.interest-distributions.calculate', compact(
            'pendingPayments',
            'totalPendingInterest',
            'proposedDistributions',
            'totalSavings',
            'activeCycle'
        ));
    }
    public function create()
    {
        $roles = InterestDistributionRole::all();
        $cycles = SavingCycle::where('status', 'active')
            ->orWhere('status', 'completed')
            ->orderBy('start_date', 'desc')
            ->get();
        $users = User::where('status', true)
            ->where('role', 'member')
            ->orderBy('name')
            ->get();

        return view('admin.interest-distributions.create', compact('roles', 'cycles', 'users'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cycle_id' => 'required|exists:saving_cycles,id',
            'distributions' => 'required|array',
            'distributions.*.user_id' => 'required|exists:users,id',
            'distributions.*.amount' => 'required|numeric|min:0',
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:loan_payments,id'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Criar distribuições
                foreach ($validated['distributions'] as $distribution) {
                    InterestDistribution::create([
                        'cycle_id' => $validated['cycle_id'],
                        'user_id' => $distribution['user_id'],
                        'amount' => $distribution['amount'],
                        'distribution_date' => now(),
                        'description' => 'Distribuição automática de juros'
                    ]);
                }

                // Marcar pagamentos como distribuídos
                LoanPayment::whereIn('id', $validated['payment_ids'])
                    ->update([
                        'distributed_at' => now(),
                        'cycle_id' => $validated['cycle_id']
                    ]);
            });

            return redirect()->route('admin.interest-distributions.index')
                ->with('success', 'Distribuição de juros realizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao realizar distribuição: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $distribution = InterestDistribution::with(['cycle', 'user'])
            ->findOrFail($id);

        $relatedPayments = LoanPayment::where('cycle_id', $distribution->cycle_id)
            ->where('distributed_at', '<=', $distribution->distribution_date)
            ->with(['loan.user'])
            ->get();

        return view('admin.interest-distributions.show', compact(
            'distribution',
            'relatedPayments'
        ));
    }

    public function report(Request $request)
    {
        $query = InterestDistribution::with(['cycle', 'user']);

        if ($request->filled('start_date')) {
            $query->where('distribution_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('distribution_date', '<=', $request->end_date);
        }

        if ($request->filled('cycle_id')) {
            $query->where('cycle_id', $request->cycle_id);
        }

        $distributions = $query->orderBy('distribution_date', 'desc')->get();

        $summary = [
            'total_distributed' => $distributions->sum('amount'),
            'total_distributions' => $distributions->count(),
            'average_distribution' => $distributions->avg('amount'),
            'period_start' => $distributions->min('distribution_date'),
            'period_end' => $distributions->max('distribution_date')
        ];

        return view('admin.interest-distributions.report', compact(
            'distributions',
            'summary'
        ));
    }
    public function export()
    {
        try {
            return Excel::download(
                new InterestDistributionsExport, 
                'distribuicoes-juros-' . now()->format('d-m-Y') . '.xlsx'
            );
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao exportar dados: ' . $e->getMessage());
        }
    }
}




   