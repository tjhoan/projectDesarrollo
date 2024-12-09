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
            'name' => 'Camisas'
        ]);
        Category::create([
            'name' => 'Pantalones'
        ]);
        Category::create([
            'name' => 'Vestidos'
        ]);
    }
}
