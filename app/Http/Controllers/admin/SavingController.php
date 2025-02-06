<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Saving;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class SavingController extends Controller
{
    public function index()
    {
        $savings = Saving::with('user')
            ->latest()
            ->paginate(10);

        $totalSavings = Saving::sum('amount');
        
        $monthlySavings = DB::table('savings')
                        ->select(
                            DB::raw('YEAR(payment_date) as year'),
                            DB::raw('MONTH(payment_date) as month'),
                            DB::raw('SUM(amount) as total')
                        )
                        ->groupBy('year', 'month')
                        ->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->limit(12)
                        ->get();


        return view('admin.savings.index', compact('savings', 'totalSavings', 'monthlySavings'));
    }

    public function create()
    {
        $users = User::where('status', true)->get();
        return view('admin.savings.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $proofFilePath = null;

        if ($request->hasFile('proof_file')) {
            $proofFilePath = $request->file('proof_file')->store('savings-proofs', 'public');
        }

        Saving::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'payment_date' => $request->date, // <- Corrigi para garantir que 'payment_date' seja salvo corretamente
            'description' => $request->description,
            'proof_file' => $proofFilePath,
        ]);

        return redirect()->route('admin.savings.index')->with('success', 'Poupança registrada com sucesso!');
    }


    public function show(Saving $saving)
    {
        $saving->load('user');
        
        $userTotalSavings = Saving::where('user_id', $saving->user_id)
            ->sum('amount');
            
        $userSavingsHistory = Saving::where('user_id', $saving->user_id)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.savings.view', compact(
            'saving', 
            'userTotalSavings',
            'userSavingsHistory'
        ));
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
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('proof_file')) {
            // Delete old file if exists
            if ($saving->proof_file) {
                Storage::disk('public')->delete($saving->proof_file);
            }
            $validated['proof_file'] = $request->file('proof_file')
                ->store('savings-proofs', 'public');
        }

        $saving->update($validated);

        return redirect()
            ->route('admin.savings.show', $saving)
            ->with('success', 'Poupança atualizada com sucesso!');
    }

    public function destroy(Saving $saving)
    {
        if ($saving->proof_file) {
            Storage::disk('public')->delete($saving->proof_file);
        }

        $saving->delete();

        return redirect()
            ->route('admin.savings.index')
            ->with('success', 'Poupança excluída com sucesso!');
    }

    public function report(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $savingsReport = DB::table('savings')
            ->join('users', 'savings.user_id', '=', 'users.id')
            ->whereBetween('savings.payment_date', [$startDate, $endDate])  // Alterado para payment_date
            ->select(
                'users.name',
                DB::raw('COUNT(*) as total_deposits'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('AVG(amount) as average_amount')
            )
            ->groupBy('users.id', 'users.name')
            ->get();

        $totalStats = [
            'total_deposits' => $savingsReport->sum('total_deposits'),
            'total_amount' => $savingsReport->sum('total_amount'),
            'average_amount' => $savingsReport->avg('average_amount')
        ];

        return view('admin.savings.report', compact(
            'savingsReport',
            'totalStats',
            'startDate',
            'endDate'
        ));
    }


    public function memberSavings($userId)
    {
        $member = User::findOrFail($userId);
        
        $savings = Saving::where('user_id', $userId)
            ->latest()
            ->paginate(10);
            
        $totalSavings = $savings->sum('amount');
        
        $monthlySavings = DB::table('savings')
            ->where('user_id', $userId)
            ->select(
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('admin.savings.member', compact(
            'member',
            'savings',
            'totalSavings',
            'monthlySavings'
        ));
    }
}