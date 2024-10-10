<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    public function run()
    {
        // Imágenes para el primer producto
        ProductImage::create(['product_id' => 1, 'image_path' => 'img/001.jpg']);
        ProductImage::create(['product_id' => 1, 'image_path' => 'img/1.1.jpg']);
        ProductImage::create(['product_id' => 1, 'image_path' => 'img/1.2.jpg']);

        // Imágenes para el segundo producto
        ProductImage::create(['product_id' => 2, 'image_path' => 'img/002.jpg']);
        ProductImage::create(['product_id' => 2, 'image_path' => 'img/2.1.jpg']);
        ProductImage::create(['product_id' => 2, 'image_path' => 'img/2.2.jpg']);

        // Imágenes para el tercer producto
        ProductImage::create(['product_id' => 3, 'image_path' => 'img/003.jpg']);
        ProductImage::create(['product_id' => 3, 'image_path' => 'img/3.1.jpg']);
        ProductImage::create(['product_id' => 3, 'image_path' => 'img/3.2.jpg']);
    }
}
