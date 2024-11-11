<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class MobileAuthLoginController extends Controller
{
    public function apiLogin(LoginRequest $request)
    {
        // Comprobar si el usuario ha alcanzado el límite de intentos de inicio de sesión
        $this->ensureIsNotRateLimited($request);

        // Autenticar al usuario con el guard 'api', que está configurado para Customer
        if (!Auth::guard('api')->attempt($request->only('email', 'password'))) {
            RateLimiter::hit($this->throttleKey($request));
            return response()->json(['message' => 'Credenciales incorrectas.'], 401);
        }

        RateLimiter::clear($this->throttleKey($request));

        // Generar el token para el usuario autenticado utilizando Sanctum
        $user = Auth::guard('api')->user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function apiLogout(Request $request)
    {
        // Revocar el token actual para el cierre de sesión
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
    }

    // Métodos de Rate Limiting para limitar intentos de login

    protected function ensureIsNotRateLimited(Request $request)
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ])->status(429);
    }

    protected function throttleKey(Request $request)
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
    }
}
