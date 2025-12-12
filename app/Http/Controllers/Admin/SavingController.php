<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Saving;
use App\Exports\SavingsExport; // Adicione esta importação
use Maatwebsite\Excel\Facades\Excel; // Adicione esta importação
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class SavingController extends Controller
{
    public function index(Request $request)
    {
        $query = Saving::with('user');

        // Filtro por usuário
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por período
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // Filtro por valor
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Ordenação
        $sortField = $request->get('sort', 'payment_date');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginação
        $savings = $query->paginate(6)->withQueryString();

        // Cálculos estatísticos
        $totalSavings = Saving::sum('amount');
        
        // Estatísticas mensais
        $monthlySavings = DB::table('savings')
            ->select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Lista de usuários para o filtro
        $users = User::where('status', true)
            ->orderBy('name')
            ->get();

        return view('admin.savings.index', compact(
            'savings',
            'totalSavings',
            'monthlySavings',
            'users',
            'request'
        ));
    }
     

    public function create()
    {
        $users = User::where('status', true)->orderBy('name')->get();
        return view('admin.savings.create', compact('users'));
    }

    public function store(Request $request)
    {
        // Validação dos dados
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            // Iniciar uma transação
            \DB::beginTransaction();

            // Criar a poupança
            $saving = new Saving();
            $saving->user_id = $validated['user_id'];
            $saving->amount = $validated['amount'];
            $saving->payment_date = $validated['date'];
            $saving->description = $validated['description'] ?? null;

            // Upload do arquivo se existir
            if ($request->hasFile('proof_file')) {
                $path = $request->file('proof_file')->store('proofs', 'public');
                $saving->proof_file = $path;
            }

            $saving->save();

            // Commit da transação
            \DB::commit();

            return redirect()
                ->route('savings.index')
                ->with('success', 'Poupança criada com sucesso!');

        } catch (\Exception $e) {
            // Rollback em caso de erro
            \DB::rollback();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao criar poupança: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $saving = Saving::findOrFail($id);
        $users = User::where('status', true)->orderBy('name')->get();
        return view('admin.savings.edit', compact('saving', 'users'));
    }

    public function update(Request $request, $id)
    {
        // Validação dos dados
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            // Iniciar uma transação
            \DB::beginTransaction();

            $saving = Saving::findOrFail($id);
            $saving->user_id = $validated['user_id'];
            $saving->amount = $validated['amount'];
            $saving->payment_date = $validated['date'];
            $saving->description = $validated['description'] ?? null;

            // Upload do novo arquivo se existir
            if ($request->hasFile('proof_file')) {
                // Deletar arquivo antigo se existir
                if ($saving->proof_file) {
                    Storage::disk('public')->delete($saving->proof_file);
                }
                
                $path = $request->file('proof_file')->store('proofs', 'public');
                $saving->proof_file = $path;
            }

            $saving->save();

            // Commit da transação
            \DB::commit();

            return redirect()
                ->route('savings.index')
                ->with('success', 'Poupança atualizada com sucesso!');

        } catch (\Exception $e) {
            // Rollback em caso de erro
            \DB::rollback();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar poupança: ' . $e->getMessage());
        }
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

    /* public function edit(Saving $saving)
    {
        $users = User::where('status', true)->get();
        return view('admin.savings.edit', compact('saving', 'users'));
    } */

    public function destroy(Saving $saving)
    {
        if ($saving->proof_file) {
            Storage::disk('public')->delete($saving->proof_file);
        }

        $saving->delete();

        return redirect()
            ->route('savings.index')
            ->with('success', 'Poupança excluída com sucesso!');
    }

    private function getMonthlyStatistics($request)
    {
        $query = DB::table('savings');
        
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        return $query->select(
            DB::raw('YEAR(payment_date) as year'),
            DB::raw('MONTH(payment_date) as month'),
            DB::raw('SUM(amount) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get();
    }
    public function report(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $startDate = $validated['start_date'] ?? now()->startOfMonth();
        $endDate = $validated['end_date'] ?? now()->endOfMonth();

        $query = DB::table('savings')
            ->join('users', 'savings.user_id', '=', 'users.id')
            ->whereBetween('savings.payment_date', [$startDate, $endDate]);

        if (isset($validated['user_id'])) {
            $query->where('users.id', $validated['user_id']);
        }

        $savingsReport = $query->select(
            'users.id',
            'users.name',
            DB::raw('COUNT(*) as total_deposits'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('AVG(amount) as average_amount'),
            DB::raw('MIN(amount) as min_amount'),
            DB::raw('MAX(amount) as max_amount')
        )
        ->groupBy('users.id', 'users.name')
        ->get();

        $totalStats = [
            'total_deposits' => $savingsReport->sum('total_deposits'),
            'total_amount' => $savingsReport->sum('total_amount'),
            'average_amount' => $savingsReport->avg('average_amount'),
            'total_members' => $savingsReport->count(),
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
    public function exportExcel(Request $request)
     {
         return Excel::download(new SavingsExport($request), 'poupancas.xlsx');
     }
 
     // Método para exportar para PDF
     public function exportPDF(Request $request)
    {
        $query = Saving::with('user');

        // Aplicar filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        $savings = $query->orderBy('payment_date', 'desc')->get();
        $totalAmount = $savings->sum('amount');

        $pdf = PDF::loadView('admin.savings.pdf', [
            'savings' => $savings,
            'totalAmount' => $totalAmount,
            'filters' => $request->all(),
            'date' => now()->format('d/m/Y H:i:s')
        ]);

        return $pdf->download('relatorio-poupancas.pdf');
    }
 
}