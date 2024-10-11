<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use Illuminate\Support\Str;

class CartSeeder extends Seeder
{
    public function run()
    {
        // Carrito para un usuario registrado
        Cart::create([
            'customer_id' => 1,
            'token' => null,
        ]);

        // Carrito para un usuario visitante (invitado)
        Cart::create([
            'customer_id' => null,
            'token' => Str::random(32),
        ]);
    }
}
