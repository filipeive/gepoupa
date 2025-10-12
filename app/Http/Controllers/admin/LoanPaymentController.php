<?php

namespace App\Http\Controllers\Admin;

use App\Models\LoanPayment;
use App\Exports\LoanPaymentsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class LoanPaymentController extends Controller
{
    /* public function index()
    {
        $payments = LoanPayment::with(['loan.user'])
            ->whereHas('loan') // Filtra apenas pagamentos com empréstimos válidos
            ->orderBy('payment_date', 'desc')
            ->paginate(10);

        return view('admin.loan-payments.index', compact('payments'));
    } */
    public function index(Request $request)
    {
        $query = LoanPayment::with(['loan.user'])
            ->when($request->search, function($query) use ($request) {
                $query->whereHas('loan.user', function($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%");
                });
            })
            ->when($request->date_from, function($query) use ($request) {
                $query->where('payment_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                $query->where('payment_date', '<=', $request->date_to);
            })
            ->when($request->amount_min, function($query) use ($request) {
                $query->where('amount', '>=', $request->amount_min);
            })
            ->when($request->amount_max, function($query) use ($request) {
                $query->where('amount', '<=', $request->amount_max);
            });

        if ($request->sort) {
            $query->orderBy($request->sort, $request->direction ?? 'asc');
        } else {
            $query->latest('payment_date');
        }

        $payments = $query->paginate(10)->withQueryString();

        if ($request->export) {
            return Excel::download(new LoanPaymentsExport($query), 'pagamentos_emprestimos.xlsx');
        }

        return view('admin.loan-payments.index', compact('payments'));
    }

    public function filter(Request $request)
    {
        $filters = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'amount_min' => 'nullable|numeric|min:0',
            'amount_max' => 'nullable|numeric|gt:amount_min'
        ]);

        return redirect()->route('loan-payments.index', $filters);
    }

    public function create()
    {
        $loans = Loan::with('user')
            ->where('status', 'approved')
            ->get();

        return view('admin.loan-payments.create', compact('loans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric|min:0',
            'interest_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('proof_file')) {
            $validated['proof_file'] = $request->file('proof_file')
                ->store('loan-payments', 'public');
        }

        $payment = LoanPayment::create($validated);

        // Verificar se o empréstimo foi totalmente pago
        $loan = Loan::find($validated['loan_id']);
        $totalPaid = $loan->payments->sum('amount') + $payment->amount; // Inclui o pagamento atual
        if ($totalPaid >= $loan->amount) {
            $loan->update(['status' => 'paid']);
        }

        return redirect()
            ->route('loan-payments.show', $payment)
            ->with('success', 'Pagamento registrado com sucesso!');
    }

    public function show(LoanPayment $loanPayment)
    {
        return view('admin.loan-payments.show', compact('loanPayment'));
    }

    public function edit(LoanPayment $loanPayment)
    {
        return view('admin.loan-payments.edit', compact('loanPayment'));
    }

    public function update(Request $request, LoanPayment $loanPayment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'interest_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('proof_file')) {
            // Deletar arquivo antigo se existir
            if ($loanPayment->proof_file) {
                Storage::disk('public')->delete($loanPayment->proof_file);
            }
            $validated['proof_file'] = $request->file('proof_file')
                ->store('loan-payments', 'public');
        }

        $loanPayment->update($validated);

        // Verificar status do empréstimo
        $loan = $loanPayment->loan;
        $totalPaid = $loan->payments->sum('amount');
        if ($totalPaid >= $loan->amount) {
            $loan->update(['status' => 'paid']);
        } else {
            $loan->update(['status' => 'approved']);
        }

        return redirect()
            ->route('loan-payments.show', $loanPayment)
            ->with('success', 'Pagamento atualizado com sucesso!');
    }

    public function destroy(LoanPayment $loanPayment)
    {
        $loan = $loanPayment->loan;

        // Deletar arquivo se existir
        if ($loanPayment->proof_file) {
            Storage::disk('public')->delete($loanPayment->proof_file);
        }

        $loanPayment->delete();

        // Verificar status do empréstimo
        $totalPaid = $loan->payments->sum('amount');
        if ($totalPaid >= $loan->amount) {
            $loan->update(['status' => 'paid']);
        } else {
            $loan->update(['status' => 'approved']);
        }

        return redirect()
            ->route('loan-payments.index')
            ->with('success', 'Pagamento excluído com sucesso!');
    }
}