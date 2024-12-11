<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    public function run()
    {
        // Imágenes para el primer producto
        ProductImage::create(['product_id' => 1, 'image_path' => 'https://neweraco.vtexassets.com/assets/vtex.file-manager-graphql/images/1f85a901-d7a2-4a2b-902c-8d691860f03e___bec6d1c0e69a9c3f84fcadc7fc2e453c.png']);
        ProductImage::create(['product_id' => 1, 'image_path' => 'https://calvincolombia.vteximg.com.br/arquivos/ids/597913-470-620/k50k510750-pf2-1-v-638344972956400000-category-product-version-image.jpg?v=638675820723400000']);
        ProductImage::create(['product_id' => 1, 'image_path' => 'https://cdn.baguer.co/uploads/2024/06/gorra-monastery-posh-cap-blanco-126828BL_A.jpg_Wmwly1edHz7Djsx3imBXaLJVJavM2TogawHPjNCC6tEN8LfiwS.jpg']);

        // Imágenes para el segundo producto
        ProductImage::create(['product_id' => 2, 'image_path' => 'https://www.texcolsport.com/wp-content/uploads/2021/05/1619816199703.jpg']);
        ProductImage::create(['product_id' => 2, 'image_path' => 'https://vansco.vteximg.com.br/arquivos/ids/340280-1000-1000/VN0A7TJPBLK-1.jpg?v=638545982599500000']);
        ProductImage::create(['product_id' => 2, 'image_path' => 'https://www.puntoblanco.co/cdn/shop/files/koni-cafe-46759-729816_046759-1.jpg?v=1720908321']);

        // Imágenes para el tercer producto
        ProductImage::create(['product_id' => 3, 'image_path' => 'https://calvincolombia.vteximg.com.br/arquivos/ids/576150-650-709/sueter-de-algodon-category-product-image.jpg?v=638672916315130000']);
        ProductImage::create(['product_id' => 3, 'image_path' => 'https://basicjeans.co/cdn/shop/products/3_3_34484209-1d51-469b-ad76-29e7ca8116e9_2048x.jpg?v=1716393757']);
        ProductImage::create(['product_id' => 3, 'image_path' => 'https://image.made-in-china.com/202f0j00nUBeCTGrhPov/Soft-Mens-Denim-Jeans-Trendy-Clothing-Casual-Wide-Leg-Cargo-Pants.webp']);
    }
}
