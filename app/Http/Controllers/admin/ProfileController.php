<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $loggedId = Auth::id();
        $user = User::find($loggedId);

        if ($user) {
            return view('admin.profile.index', compact('user'));
        } else {
            return redirect()->route('admin');
        }
    }

    public function save(Request $request, $id){
        $user = User::find($id);

        if($user){
            $data = $request->only(['name', 'phone', 'role', 'status']); 

            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }

            $user->update($data);

            return redirect()->route('profile')->with('success', 'Perfil salvo com sucesso!');
        } else {
            return redirect('admin');
        }
    }
}
