<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Saving;
use App\Models\SocialFund;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Models\User;


class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('can:edit-users');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obter o valor da pesquisa
        $search = $request->get('search');

        // Filtrar os usuários pelo nome ou email
        $users = User::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })->paginate(8);
        $loggedId = intval(Auth::id());

        // Retornar JSON se for uma requisição AJAX
        if ($request->ajax()) {
            return response()->json([

                'users' => $users,
                'pagination' => (string) $users->links('pagination::bootstrap-5'),
            ]);
        }

        // Retornar a view padrão para requisições normais
        return view('admin.users.index', compact('users', 'search', 'loggedId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mostrar o formulário para criar um novo usuário
        return view('admin.users.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,member',
            'status' => 'boolean',
            'password' => 'required|min:8|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);
        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Cálculo de poupanças
        $totalSavings = $user->savings()->sum('amount');

        // Cálculo de fundos sociais
        $totalSocialFunds = $user->socialFunds()->sum('amount');

        // Empréstimos ativos (apenas aprovados)
        $activeLoans = $user->loans()
            ->where('status', 'approved')
            ->get();

        // Cálculos adicionais
        $currentYearSavings = $user->savings()
            ->whereYear('payment_date', date('Y'))
            ->sum('amount');

        $pendingSocialFunds = $user->socialFunds()
            ->where('status', 'pending')
            ->sum('amount');

        // Estatísticas de empréstimos
        $loanStats = [
            'total' => $user->loans()->count(),
            'active' => $user->loans()->where('status', 'approved')->count(),
            'paid' => $user->loans()->where('status', 'paid')->count(),
            'pending' => $user->loans()->where('status', 'pending')->count()
        ];

        return view('admin.users.view', compact(
            'user',
            'totalSavings',
            'totalSocialFunds',
            'activeLoans',
            'currentYearSavings',
            'pendingSocialFunds',
            'loanStats'
        ));
    }
    // Método opcional para exportar dados
    public function export(User $user)
    {
        // Implementar exportação de dados se necessário
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Mostrar o formulário para editar o usuário
        $user = User::find($id);

        // Verificar se o id existe
        if ($user) {
            return view('admin.users.edit', compact('user'));
        } else {
            return redirect()->route('users.index')->with('deleted', 'Usuário não encontrado.');
        }
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('users.index')->with('error', 'Usuário não encontrado.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,member',
            'status' => 'boolean',
            'admin' => 'tiny',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Update the specified resource in storage.
     */
    /* public function update(Request $request, string $id)
    {
        // Validar os dados do formulário
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Senha opcional
        ]);

        // Buscar o usuário
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('users.index')->with('error', 'Usuário não encontrado.');
        }

        // Atualizar os dados do usuário
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        // Atualizar a senha apenas se for fornecida
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Salvar alterações
        $user->save();

        // Redirecionar com mensagem de sucesso
        return redirect()->route('users.index')->with('updated', 'Usuário atualizado com sucesso!');
    }  */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $loggedId = intval(Auth::id());

            if ($loggedId == $id) {
                return redirect()
                    ->route('users.index')
                    ->with('error', 'Você não pode excluir seu próprio cadastro.');
            }

            $user = User::findOrFail($id);
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuário excluído com sucesso!');

        } catch (\Exception $e) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Erro ao excluir usuário: ' . $e->getMessage());
        }
    }
}
