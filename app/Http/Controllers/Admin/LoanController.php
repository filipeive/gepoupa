<?php
namespace App\Http\Controllers\Admin;

use App\Exports\LoansExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with(['user', 'payments'])
            ->whereHas('user')
            ->orderBy('request_date', 'desc');

        // Filtros
        if ($request->filled('status')) { // Verifica se o campo "status" foi preenchido
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) { // Verifica se o campo "user_id" foi preenchido
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('start_date')) { // Verifica se o campo "start_date" foi preenchido
            $query->where('request_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) { // Verifica se o campo "end_date" foi preenchido
            $query->where('request_date', '<=', $request->end_date);
        }

        $loans = $query->paginate(10);

        // Estatísticas
        $loanStats = [
            'total' => Loan::count(),
            'active' => Loan::where('status', 'approved')->count(),
            'pending' => Loan::where('status', 'pending')->count(),
            'paid' => Loan::where('status', 'paid')->count(),
        ];

        // Lista de membros ativos
        $members = User::where('role', 'member')->where('status', true)->get();

        return view('admin.loans.index', compact('loans', 'loanStats', 'members'));
    }

    public function create()
    {
        // Pega todos os membros (usuários) ativos para selecionar no formulário
        $members = User::where('role', 'member')->where('status', true)->get();

        return view('admin.loans.create', compact('members'));
    }

    public function store(Request $request)
    {
        // Validação dos dados do formulário
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'request_date' => 'required|date',
            'due_date' => 'required|date|after:request_date',
        ]);

        // Inicializar saldo devedor com o valor do empréstimo
        $validated['current_balance'] = $validated['amount'];
        $validated['last_interest_calculation_date'] = $validated['request_date'];

        // Criação do empréstimo
        $loan = Loan::create($validated);

        return redirect()
            ->route('loans.show', $loan)
            ->with('success', 'Empréstimo registrado com sucesso!');
    }

    public function show(Loan $loan)
    {
        $loan->load(['user', 'payments']);

        $totalPaid = $loan->payments->sum('amount');
        $totalInterest = $loan->payments->sum('interest_amount');
        // Calcular saldo devedor dinamicamente para garantir precisão na visualização
        $principalPaid = $totalPaid - $totalInterest;
        $remainingAmount = $loan->amount - $principalPaid;

        // Opcional: Atualizar o current_balance no banco se estiver inconsistente (apenas para correção)
        // if (abs($loan->current_balance - $remainingAmount) > 0.01) {
        //     $loan->current_balance = $remainingAmount;
        //     $loan->save();
        // }

        return view('admin.loans.show', compact(
            'loan',
            'totalPaid',
            'totalInterest',
            'remainingAmount'
        ));
    }
    public function edit(Loan $loan)
    {
        return view('admin.loans.edit', compact('loan'));
    }
    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,paid',
        ]);

        $loan->update($validated);

        return redirect()->route('loans.index')
            ->with('success', 'Status do empréstimo atualizado com sucesso!');
    }

    public function registerPayment(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'interest_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('proof_file')) {
            $validated['proof_file'] = $request->file('proof_file')
                ->store('loan-payments', 'public');
        }

        $payment = $loan->payments()->create($validated);

        // Atualizar saldo devedor
        // O pagamento reduz o saldo devedor (principal)
        // O valor pago (amount) inclui juros? Geralmente sim.
        // Se amount é o total pago, e interest_amount é a parte dos juros:
        // Principal pago = amount - interest_amount

        $principalPaid = $validated['amount'] - $validated['interest_amount'];

        if ($principalPaid > 0) {
            $loan->current_balance -= $principalPaid;
            if ($loan->current_balance < 0)
                $loan->current_balance = 0;
        }

        $loan->save();

        // Verificar se o empréstimo foi totalmente pago
        if ($loan->current_balance <= 0) {
            $loan->update(['status' => 'paid']);
        }

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Pagamento registrado com sucesso!');
    }
    public function export()
    {
        return Excel::download(new LoansExport, 'emprestimos.xlsx');
    }
}
