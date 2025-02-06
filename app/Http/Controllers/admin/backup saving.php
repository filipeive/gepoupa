<?php
/* 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Saving;
use App\Models\SocialFund;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SavingController extends Controller
{
    public function index()
    {
        $savings = Saving::with(['user', 'socialFund'])
            ->orderBy('payment_date', 'desc')
            ->paginate(10);
    
        $totalSavings = Saving::sum('amount');
        $pendingSocialFunds = SocialFund::where('status', 'pending')->count();
        
        return view('admin.savings.index', compact('savings', 'totalSavings', 'pendingSocialFunds'));
    }

    public function create()
    {
        $users = User::where('status', true)->get();
        return view('admin.savings.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'proof_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // Criar a poupança
            if ($request->hasFile('proof_file')) {
                $path = $request->file('proof_file')->store('proofs/savings', 'public');
                $validated['proof_file'] = $path;
            }

            $saving = Saving::create($validated);

            // Verificar e criar fundo social
            $this->handleSocialFund($validated['user_id'], $validated['payment_date']);

            DB::commit();

            return redirect()->route('admin.savings.index')
                ->with('success', 'Poupança registrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao registrar poupança: ' . $e->getMessage())
                ->withInput();
        }
    }

    protected function handleSocialFund($userId, $paymentDate)
    {
        $paymentDate = Carbon::parse($paymentDate);
        $monthYear = $paymentDate->format('Y-m');

        // Verificar se já existe fundo social para este mês
        $existingSocialFund = SocialFund::where('user_id', $userId)
            ->whereYear('payment_date', $paymentDate->year)
            ->whereMonth('payment_date', $paymentDate->month)
            ->first();

        if (!$existingSocialFund) {
            // Criar novo fundo social
            $amount = 100; // Valor fixo de 100 MZN
            
            // Se a data de pagamento for depois do último dia do mês
            if ($paymentDate->isAfter($paymentDate->copy()->endOfMonth())) {
                $penalty = $amount * 0.10; // 10% de multa por semana
                $weeksLate = $paymentDate->diffInWeeks($paymentDate->copy()->endOfMonth());
                $penalty = $penalty * $weeksLate;
                
                SocialFund::create([
                    'user_id' => $userId,
                    'amount' => $amount,
                    'payment_date' => $paymentDate,
                    'status' => 'late',
                    'penalty_amount' => $penalty
                ]);
            } else {
                SocialFund::create([
                    'user_id' => $userId,
                    'amount' => $amount,
                    'payment_date' => $paymentDate,
                    'status' => 'paid'
                ]);
            }
        }
    }

    public function show(Saving $saving)
    {
        $saving->load(['user', 'socialFund']);
        return view('admin.savings.view', compact('saving'));
    }

    public function edit(Saving $saving)
    {
        $users = User::where('status', true)->get();
        return view('admin.savings.edit', compact('saving', 'users'));
    }

    public function update(Request $request, Saving $saving)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('proof_file')) {
            if ($saving->proof_file) {
                Storage::disk('public')->delete($saving->proof_file);
            }
            $path = $request->file('proof_file')->store('proofs/savings', 'public');
            $validated['proof_file'] = $path;
        }

        $saving->update($validated);

        return redirect()->route('admin.savings.index')
            ->with('success', 'Poupança atualizada com sucesso!');
    }

    public function destroy(Saving $saving)
    {
        if ($saving->proof_file) {
            Storage::disk('public')->delete($saving->proof_file);
        }
        
        $saving->delete();

        return redirect()->route('admin.savings.index')
            ->with('success', 'Poupança excluída com sucesso!');
    }
} */