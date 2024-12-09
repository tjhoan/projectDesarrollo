<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (Auth::guard('admin')->check()) {
            Log::info('Admin autenticado', ['user' => Auth::guard('admin')->user()]);
            return redirect()->intended('/admin/dashboard');
        }

        if (Auth::guard('web')->check()) {
            Log::info('Cliente autenticado', ['user' => Auth::guard('web')->user()]);
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        Log::error('Fallo en la autenticación');
    }


    public function destroy(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } else {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
