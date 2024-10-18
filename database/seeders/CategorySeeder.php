<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Creamos algunas categorías
        Category::create([
            'name' => 'Camisas',
            'description' => 'Categoría de Camisas.'
        ]);
        Category::create([
            'name' => 'Pantalones',
            'description' => 'Categoría de pantalones.'
        ]);
        Category::create([
            'name' => 'Vestidos',
            'description' => 'Categoría de vestidos.'
        ]);
    }
}
