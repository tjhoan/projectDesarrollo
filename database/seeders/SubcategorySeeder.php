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
        $category1 = Category::where('name', 'Ropa de Hombre')->first();
        $category2 = Category::where('name', 'Ropa de Mujer')->first();
        $category3 = Category::where('name', 'Accesorios')->first();

        // Añadiendo subcategorías para 'Ropa de Hombre'
        Subcategory::create(['name' => 'Camisas', 'category_id' => $category1->id]);
        Subcategory::create(['name' => 'Pantalones', 'category_id' => $category1->id]);

        // Añadiendo subcategorías para 'Ropa de Mujer'
        Subcategory::create(['name' => 'Vestidos', 'category_id' => $category2->id]);
        Subcategory::create(['name' => 'Faldas', 'category_id' => $category2->id]);

        // Añadiendo subcategorías para 'Accesorios'
        Subcategory::create(['name' => 'Sombreros', 'category_id' => $category3->id]);
        Subcategory::create(['name' => 'Cinturones', 'category_id' => $category3->id]);
    }
}
