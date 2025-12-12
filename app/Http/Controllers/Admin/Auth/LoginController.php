<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/painel';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function index(){
        // Implementar lógica de login
        return view('admin.login');
    }
    public function authenticate(Request $request)
{
    // Validação dos dados de login
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],  
    ]);

    // Autenticação com suporte ao "remember me"
    $remember = $request->input('remember', false); // Verifica se o checkbox "Lembrar-me" foi marcado
    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate(); // Regenera a sessão para evitar fixation attacks
        return redirect()->intended('/painel');
    }

    // Redireciona de volta com erro caso a autenticação falhe
    return back()->withErrors([
        'email' => 'As credenciais fornecidas estão incorretas.',
    ])->onlyInput('email');
}



    public function logout()
    {
        // Implementar lógica de logout
        Auth::logout();
        return redirect()->route('login');
    }
    public function Validator(array $data, $isLogin = false)
    {
        if ($isLogin) {
            // Validações para login
            return \Validator::make($data, [
                'email' => ['required', 'string', 'email', 'max:100'],
                'password' => ['required', 'string', 'min:8'], // Sem 'confirmed'
            ]);
        }
    
        // Validações para registro
        return \Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    } 
}
