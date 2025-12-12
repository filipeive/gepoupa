<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        foreach ($roles as $role) {
            if (Auth::user()->role === $role) {
                return $next($request);
            }
        }

        return redirect('/')->with('error', 'Você não tem permissão para acessar essa área.');
    }
}