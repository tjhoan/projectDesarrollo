<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminLoginController extends Controller
{
    public function login(Request $request)
    {
        Log::info('Intento de inicio de sesión', ['email' => $request->email]);

        // Intentar iniciar sesión como admin
        if (Auth::guard('admin')->attempt($request->only('email', 'password'), $request->remember)) {
            Log::info('Inicio de sesión exitoso', ['email' => $request->email]);
            return redirect()->intended('products.index');
        }

        Log::error('Fallo el inicio de sesión', ['email' => $request->email]);
        return back()->withErrors(['email' => 'Estas credenciales no coinciden con nuestros registros.']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('home');
    }
}
