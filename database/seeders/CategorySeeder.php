<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Creamos algunas categorías
        Category::create(['name' => 'Ropa de Hombre', 'description' => 'Categoría de ropa para hombres.']);
        Category::create(['name' => 'Ropa de Mujer', 'description' => 'Categoría de ropa para mujeres.']);
        Category::create(['name' => 'Accesorios', 'description' => 'Accesorios diversos para todos.']);
    }
}
