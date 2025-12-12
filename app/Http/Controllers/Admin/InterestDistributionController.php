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

    public function create()
    {
        // 1. Calcular Total de Juros Arrecadados no Ano (ou Ciclo)
        // Assumindo que distribuímos tudo que foi pago e ainda não distribuído
        $pendingPayments = LoanPayment::with(['loan.user'])
            ->whereNull('distributed_at')
            ->get();

        $totalInterestCollected = $pendingPayments->sum('interest_amount');

        // 2. Identificar Membros com e sem dívida
        // "Membros com dívida" são aqueles que pagaram juros neste período?
        // Ou que têm empréstimos ativos?
        // A regra diz: "Members who had debt during the year".
        // Vamos considerar quem fez pagamentos de juros neste lote como "tendo dívida".
        // E quem não fez, mas tem poupança, como "sem dívida".

        $debtorUserIds = $pendingPayments->pluck('loan.user_id')->unique();

        // Membros ativos
        $allMembers = User::where('role', 'member')->where('status', true)->get();

        $distributions = [];

        // 3. Lógica de Distribuição

        // Pool de 15% para quem não teve dívida
        $poolForNonDebtors = $totalInterestCollected * 0.15;

        // Calcular total de poupança dos não-devedores para rateio
        $nonDebtors = $allMembers->whereNotIn('id', $debtorUserIds);
        $totalSavingsNonDebtors = 0;
        foreach ($nonDebtors as $member) {
            $totalSavingsNonDebtors += $member->savings()->sum('amount');
        }

        foreach ($allMembers as $member) {
            $amount = 0;
            $description = '';

            if ($debtorUserIds->contains($member->id)) {
                // É devedor: Recebe 85% dos juros que pagou
                $interestPaidByMember = $pendingPayments->where('loan.user_id', $member->id)->sum('interest_amount');
                $amount = $interestPaidByMember * 0.85;
                $description = "Devolução de 85% dos juros pagos ({$interestPaidByMember})";
            } else {
                // Não é devedor: Recebe parte dos 15% proporcional à poupança
                $memberSavings = $member->savings()->sum('amount');
                if ($totalSavingsNonDebtors > 0) {
                    $share = $memberSavings / $totalSavingsNonDebtors;
                    $amount = $poolForNonDebtors * $share;
                    $description = "Participação de " . round($share * 100, 2) . "% no fundo de 15%";
                }
            }

            // 4. Desconto Fixo de 31 MZN
            $finalAmount = $amount - 31;
            $description .= " - Taxa fixa (31.00)";

            // Se o valor final for negativo, o membro deve? Ou fica zero?
            // Geralmente debita da poupança ou fica zero. Vamos assumir que fica zero na distribuição,
            // mas idealmente deveria ser cobrado. Por enquanto, permitimos negativo ou zeramos?
            // Vamos manter o valor real para o admin decidir, mas no display mostrar alerta.

            $distributions[] = [
                'user_id' => $member->id,
                'name' => $member->name,
                'is_debtor' => $debtorUserIds->contains($member->id),
                'gross_amount' => $amount,
                'deduction' => 31,
                'net_amount' => $finalAmount,
                'description' => $description
            ];
        }

        $cycles = SavingCycle::where('status', 'active')->get();

        return view('admin.interest-distributions.calculate', compact(
            'distributions',
            'totalInterestCollected',
            'poolForNonDebtors',
            'totalSavingsNonDebtors',
            'cycles'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cycle_id' => 'required|exists:saving_cycles,id',
            'distributions' => 'required|array',
            'distributions.*.user_id' => 'required|exists:users,id',
            'distributions.*.amount' => 'required|numeric', // Pode ser negativo
            'distributions.*.description' => 'required|string',
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
                        'description' => $distribution['description']
                    ]);
                }

                // Marcar TODOS os pagamentos pendentes como distribuídos
                // (Simplificação: assumindo que distribuímos tudo que estava pendente)
                LoanPayment::whereNull('distributed_at')
                    ->update([
                        'distributed_at' => now(),
                        'cycle_id' => $validated['cycle_id']
                    ]);
            });

            return redirect()->route('interest-distribution.index')
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




