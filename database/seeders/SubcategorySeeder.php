<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class SubcategorySeeder extends Seeder
{
    public function run()
    {
        // Obteniendo las categorías ya creadas por el seeder de categorías
        $category1 = Category::where('name', 'Camisas')->first();
        $category2 = Category::where('name', 'Pantalones')->first();
        $category3 = Category::where('name', 'Vestidos')->first();

        // Añadiendo subcategorías para 'Camisas'
        Subcategory::create(['name' => 'Hombres', 'category_id' => $category1->id]);
        Subcategory::create(['name' => 'Mujeres', 'category_id' => $category1->id]);
        Subcategory::create(['name' => 'Niños', 'category_id' => $category1->id]);

        // Añadiendo subcategorías para 'Pantalones'
        Subcategory::create(['name' => 'Hombres', 'category_id' => $category2->id]);
        Subcategory::create(['name' => 'Mujeres', 'category_id' => $category2->id]);
        Subcategory::create(['name' => 'Niños', 'category_id' => $category2->id]);
    
        // Añadiendo subcategorías para 'Vestidos'
        Subcategory::create(['name' => 'Mujeres', 'category_id' => $category3->id]);
        Subcategory::create(['name' => 'Niñas', 'category_id' => $category3->id]);
    }
}
