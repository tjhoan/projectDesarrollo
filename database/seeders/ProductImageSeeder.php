<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    public function run()
    {
        // Imágenes para el primer producto
        ProductImage::create(['product_id' => 1, 'image_path' => 'https://seguridadindustrialymedica.com/wp-content/uploads/2020/01/camisa-oxford-azul.jpg']);
        ProductImage::create(['product_id' => 1, 'image_path' => 'https://seguridadindustrialymedica.com/wp-content/uploads/2020/01/camisa-oxford-azul.jpg']);
        ProductImage::create(['product_id' => 1, 'image_path' => 'https://seguridadindustrialymedica.com/wp-content/uploads/2020/01/camisa-oxford-azul.jpg']);

        // Imágenes para el segundo producto
        ProductImage::create(['product_id' => 2, 'image_path' => 'https://seguridadindustrialymedica.com/wp-content/uploads/2020/01/camisa-oxford-azul.jpg']);
        ProductImage::create(['product_id' => 2, 'image_path' => 'https://seguridadindustrialymedica.com/wp-content/uploads/2020/01/camisa-oxford-azul.jpg']);
        ProductImage::create(['product_id' => 2, 'image_path' => 'https://seguridadindustrialymedica.com/wp-content/uploads/2020/01/camisa-oxford-azul.jpg']);

        // Imágenes para el tercer producto
        ProductImage::create(['product_id' => 3, 'image_path' => 'https://seguridadindustrialymedica.com/wp-content/uploads/2020/01/camisa-oxford-azul.jpg']);
        ProductImage::create(['product_id' => 3, 'image_path' => 'https://seguridadindustrialymedica.com/wp-content/uploads/2020/01/camisa-oxford-azul.jpg']);
        ProductImage::create(['product_id' => 3, 'image_path' => 'https://seguridadindustrialymedica.com/wp-content/uploads/2020/01/camisa-oxford-azul.jpg']);
    }
}
