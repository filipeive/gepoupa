<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialFund;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\SocialFundRequest;
use Illuminate\Support\Facades\Storage;

class SocialFundController extends Controller
{
    public function index(Request $request)
    {
        $query = SocialFund::with('user');

        // Implementa a busca
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Dados para a tabela principal
        $socialFunds = $query->latest()
            ->paginate(10);

        // Dados para os cards de estatísticas
        $statistics = [
            'totalFunds' => SocialFund::where('status', 'paid')->sum('amount'),
            'approvedFunds' => SocialFund::where('status', 'paid')->sum('amount'),
            'pendingFunds' => SocialFund::where('status', 'pending')->sum('amount'),
            'membersWithoutPayment' => $this->getMembersWithoutPayment()
        ];

        // Obtém todos os usuários para preencher o dropdown no modal
        $users = User::all();

        // Retorna a view com todos os dados
        return view('admin.social-funds.index', array_merge(
            ['socialFunds' => $socialFunds, 'users' => $users],
            $statistics
        ));
    }


    public function create()
    {
        //echo "Criar SocialFund";
        $users = User::where('status', true)
            ->where('role', 'member')
            ->get();

        return view('admin.social-funds.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:100', // Enforce minimum 100
            'payment_date' => 'required|date',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'required|in:pending,paid,late',
        ]);

        if ($request->hasFile('proof_file')) {
            $validated['proof_file'] = $request->file('proof_file')->store('social-funds', 'public');
        }

        // Calcular multa se estiver atrasado
        if ($validated['status'] === 'late') {
            // Assumindo que a data de vencimento era o último dia do mês anterior ou uma data fixa
            // Para simplificar, vamos calcular semanas de atraso baseadas na data atual vs payment_date se for passado
            // Mas a regra diz "10% weekly penalty". Vamos assumir que o usuário define se é late, e o sistema calcula.
            // Melhor: Se status é late, calcular multa baseada na diferença entre data de pagamento e data devida.
            // Como não temos "due_date" explícito no form, vamos assumir que a data devida era o dia 25 do mês de referência.

            $paymentDate = \Carbon\Carbon::parse($validated['payment_date']);
            $dueDate = $paymentDate->copy()->startOfMonth()->addDays(24); // Dia 25

            if ($paymentDate->gt($dueDate)) {
                $weeksLate = $paymentDate->diffInWeeks($dueDate);
                if ($weeksLate > 0) {
                    $penalty = 100 * 0.10 * $weeksLate; // 10% de 100 MZN por semana
                    $validated['penalty_amount'] = $penalty;
                }
            }
        }

        // Criação do fundo social
        $socialFund = SocialFund::create($validated);

        // Redirecionar para a lista de fundos sociais com uma mensagem de sucesso
        return redirect()->route('social-funds.index')
            ->with('success', 'Fundo Social criado com sucesso!');
    }

    public function show(SocialFund $socialFund)
    {
        $socialFund->load('user');

        // Busca o histórico de pagamentos do membro
        $memberHistory = SocialFund::where('user_id', $socialFund->user_id)
            ->where('id', '!=', $socialFund->id)
            ->latest('payment_date')
            ->take(5)
            ->get();

        return view('admin.social-funds.show', compact('socialFund', 'memberHistory'));
    }

    public function edit(SocialFund $socialFund)
    {
        $users = User::where('status', true)
            ->where('role', 'member')
            ->get();

        return view('admin.social-funds.edit', compact('socialFund', 'users'));
    }

    public function update(SocialFundRequest $request, SocialFund $socialFund)
    {
        $data = $request->validated();

        if ($request->hasFile('proof_file')) {
            // Remove arquivo antigo se existir
            if ($socialFund->proof_file) {
                Storage::disk('public')->delete($socialFund->proof_file);
            }

            $data['proof_file'] = $request->file('proof_file')
                ->store('social-funds', 'public');
        }

        $socialFund->update($data);

        return redirect()
            ->route('social-funds.index')
            ->with('success', 'Pagamento atualizado com sucesso!');
    }

    public function destroy(SocialFund $socialFund)
    {
        if ($socialFund->proof_file) {
            Storage::disk('public')->delete($socialFund->proof_file);
        }

        $socialFund->delete();

        return redirect()
            ->route('social-funds.index')
            ->with('success', 'Pagamento excluído com sucesso!');
    }

    private function getMembersWithoutPayment()
    {
        return User::where('role', 'member')
            ->whereDoesntHave('socialFunds', function ($query) {
                $query->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year);
            })
            ->count();
    }
}