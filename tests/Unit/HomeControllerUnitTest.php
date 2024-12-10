<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function carga_categorias_y_productos_en_la_pagina_de_inicio()
    {
        // Crear categorías y productos
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        ProductImage::factory()->create(['product_id' => $product->id, 'image_path' => 'path/to/image.jpg']);

        // Realizar la solicitud al HomeController
        $response = $this->get(route('home'));

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que las categorías y productos se están pasando a la vista
        $response->assertViewHas('categories');
        $response->assertViewHas('products');
        $response->assertSee($product->name);
        $response->assertSee($category->name);
    }

    /** @test */
    public function filtra_productos_por_categoria()
    {
        // Crear categorías y productos
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $product1 = Product::factory()->create(['category_id' => $category1->id]);
        $product2 = Product::factory()->create(['category_id' => $category2->id]);

        // Realizar la solicitud con el filtro por categoría
        $response = $this->get(route('home') . '?category_id=' . $category1->id);

        // Verificar que los productos de la categoría 1 se muestren
        $response->assertSee($product1->name);
        $response->assertDontSee($product2->name);
    }

    /** @test */
    public function carga_pagina_de_detalles_del_producto()
    {
        // Crear producto
        $product = Product::factory()->create();
        ProductImage::factory()->create(['product_id' => $product->id, 'image_path' => 'path/to/image.jpg']);

        // Realizar la solicitud a la página de detalles del producto
        $response = $this->get(route('products.details', $product->id));

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que los detalles del producto se están pasando a la vista
        $response->assertViewHas('product');
        $response->assertSee($product->name);
        $response->assertSee($product->description);
    }

    /** @test */
    public function muestra_imagen_por_defecto_cuando_el_producto_no_tiene_imagenes()
    {
        // Crear producto sin imágenes
        $product = Product::factory()->create();

        // Realizar la solicitud a la página de detalles del producto
        $response = $this->get(route('products.details', $product->id));

        // Verificar que se muestre la imagen por defecto
        $response->assertSee('default.png');
    }
}
