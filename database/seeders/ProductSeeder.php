<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Creamos algunos productos
        Product::create([
            'name' => 'Camiseta Básica',
            'price' => 20.00,
            'quantity' => 50,
            'brand' => 'BasicBrand',
            'category_id' => 1,
            'description' => 'Camiseta de algodón básica para el uso diario.'
        ]);

        Product::create([
            'name' => 'Blusa Casual',
            'price' => 30.00,
            'quantity' => 30,
            'brand' => 'FashionLine',
            'category_id' => 2,
            'description' => 'Blusa casual perfecta para cualquier ocasión.'
        ]);

        Product::create([
            'name' => 'Gorra Deportiva',
            'price' => 15.00,
            'quantity' => 20,
            'brand' => 'SportX',
            'category_id' => 3,
            'description' => 'Gorra ideal para deportes al aire libre.'
        ]);
    }
}
