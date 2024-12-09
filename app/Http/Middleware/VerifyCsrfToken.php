<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'cart/*',  // Excluir todas las rutas del carrito para verificar si el problema es por CSRF
    ];
}
