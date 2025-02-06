<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['user', 'payments'])
            ->whereHas('user') // Filtra apenas empréstimos com usuários válidos
            ->orderBy('request_date', 'desc')
            ->paginate(10);

        $loanStats = [
            'total' => Loan::count(),
            'active' => Loan::where('status', 'approved')->count(),
            'pending' => Loan::where('status', 'pending')->count(),
            'paid' => Loan::where('status', 'paid')->count(),
        ];

        return view('admin.loans.index', compact('loans', 'loanStats'));
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

        // Criação do empréstimo
        $loan = Loan::create($validated);

        return redirect()
            ->route('admin.loans.show', $loan)
            ->with('success', 'Empréstimo registrado com sucesso!');
    }

    public function show(Loan $loan)
    {
        $loan->load(['user', 'payments']);

        $totalPaid = $loan->payments->sum('amount');
        $totalInterest = $loan->payments->sum('interest_amount');
        $remainingAmount = $loan->amount + ($loan->amount * ($loan->interest_rate / 100)) - $totalPaid;

        return view('admin.loans.show', compact(
            'loan',
            'totalPaid',
            'totalInterest',
            'remainingAmount'
        ));
    }

    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,paid',
        ]);

        $loan->update($validated);

        return redirect()->route('admin.loans.index')
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

        $loan->payments()->create($validated);

        // Verificar se o empréstimo foi totalmente pago
        $totalPaid = $loan->payments->sum('amount');
        if ($totalPaid >= $loan->amount) {
            $loan->update(['status' => 'paid']);
        }

        return redirect()->route('admin.loans.show', $loan)
            ->with('success', 'Pagamento registrado com sucesso!');
    }
}
