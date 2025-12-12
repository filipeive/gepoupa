<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        //redirect to login page from admin
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        //return view('site.home');
    }
}
