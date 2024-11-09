<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileAuthLoginController extends Controller
{
    public function apiLogin(LoginRequest $request)
    {
        $request->authenticate();

        // Regenerar la sesión
        $request->session()->regenerate();

        // Retornar el usuario autenticado y un token de autenticación
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => Auth::user(),
        ], 200);
    }

    public function apiLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
    }
}
