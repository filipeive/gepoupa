<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingDistribution;
use App\Models\SavingCycle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavingDistributionController extends Controller
{
    public function index()
    {
        $distributions = SavingDistribution::with(['cycle', 'user'])
            ->orderBy('distribution_date', 'desc')
            ->paginate(10);

        return view('admin.saving-distributions.index', compact('distributions'));
    }

    public function create(Request $request)
    {
        $cycles = SavingCycle::where('status', 'active')->get();
        $selectedCycle = null;
        $members = collect();

        if ($request->has('cycle_id')) {
            $selectedCycle = SavingCycle::findOrFail($request->cycle_id);

            // Calculate total savings for each member in this cycle
            // Assuming savings are linked to users, and we want to distribute all savings for the cycle period
            // Or if savings have a cycle_id (they don't seem to, based on schema, they have payment_date)

            $members = User::where('role', 'member')
                ->where('status', true)
                ->get()
                ->map(function ($user) use ($selectedCycle) {
                    $totalSaved = $user->savings()
                        ->whereBetween('payment_date', [$selectedCycle->start_date, $selectedCycle->end_date])
                        ->sum('amount');

                    $user->total_saved_in_cycle = $totalSaved;

                    // Calculate total debt (active loans)
                    $user->total_debt = $user->loans()
                        ->whereIn('status', ['approved', 'pending'])
                        ->where('current_balance', '>', 0)
                        ->sum('current_balance');

                    // Net value: Savings - Debt
                    // If positive, user receives money.
                    // If negative, user still owes money (and savings are used to pay part of debt).
                    $user->net_distribution = $totalSaved - $user->total_debt;

                    return $user;
                })
                ->filter(function ($user) {
                    // Show all users who have savings OR debt, so we can see the full picture
                    return $user->total_saved_in_cycle > 0 || $user->total_debt > 0;
                });
        }

        return view('admin.saving-distributions.create', compact('cycles', 'selectedCycle', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cycle_id' => 'required|exists:saving_cycles,id',
            'distributions' => 'required|array',
            'distributions.*.user_id' => 'required|exists:users,id',
            'distributions.*.amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['distributions'] as $distribution) {
                $user = User::findOrFail($distribution['user_id']);
                $totalSaved = $distribution['amount'];

                // 1. Calculate Debt
                $activeLoans = $user->loans()
                    ->whereIn('status', ['approved', 'pending'])
                    ->where('current_balance', '>', 0)
                    ->orderBy('request_date', 'asc') // Pay oldest first
                    ->get();

                $remainingSavings = $totalSaved;

                // 2. Pay off loans with savings
                foreach ($activeLoans as $loan) {
                    if ($remainingSavings <= 0)
                        break;

                    $amountToPay = min($remainingSavings, $loan->current_balance);

                    if ($amountToPay > 0) {
                        // Create Payment Record
                        $loan->payments()->create([
                            'amount' => $amountToPay,
                            'interest_amount' => 0, // Assuming savings pay principal first? Or should we calculate interest?
                            // Let's assume it pays whatever is outstanding. 
                            // If we want to be precise, we should check accumulated interest.
                            // For simplicity, we treat it as a generic payment reducing balance.
                            // But wait, registerPayment logic subtracts interest_amount from amount to get principal.
                            // If we set interest_amount = 0, then Principal Paid = amount.
                            // This reduces current_balance by amount. Correct.
                            'payment_date' => now(),
                            'notes' => 'Liquidação automática via Poupança'
                        ]);

                        $loan->current_balance -= $amountToPay;
                        if ($loan->current_balance <= 0) {
                            $loan->current_balance = 0;
                            $loan->status = 'paid';
                        }
                        $loan->save();

                        $remainingSavings -= $amountToPay;
                    }
                }

                // 3. Record Distribution
                // We record the total savings processed.
                // The "net result" is implied by the remaining debt (which persists) or the cash given (remainingSavings).
                // But the user wants to see "negative" if they still owe.
                // The SavingDistribution table stores "total_saved".
                // Maybe we should store "amount_distributed" (cash given) and "amount_liquidated" (debt paid)?
                // For now, let's stick to the schema and just record the event.

                SavingDistribution::create([
                    'cycle_id' => $validated['cycle_id'],
                    'user_id' => $distribution['user_id'],
                    'total_saved' => $totalSaved,
                    'distribution_date' => now(),
                ]);
            }
        });

        return redirect()->route('saving-distributions.index')
            ->with('success', 'Distribuição de poupança realizada com sucesso!');
    }
}
