<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Intentar iniciar sesión como admin
        if (Auth::guard('admin')->attempt($request->only('email', 'password'), $request->remember)) {
            return redirect()->intended('/admin/dashboard');  // Redirigir a la ruta de administración
        }

        // Si la autenticación falla, redirigir de vuelta con un mensaje de error
        return back()->withErrors(['email' => 'Estas credenciales no coinciden con nuestros registros.']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('home');
    }
}
