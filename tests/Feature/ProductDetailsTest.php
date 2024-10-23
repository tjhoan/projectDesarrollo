<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;

class ProductDetailsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function carga_correctamente_la_pagina_de_detalles_del_producto()
    {
        // Crear categoría y producto de prueba
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        // Hacer una petición GET a la vista de detalles del producto
        $response = $this->get(route('products.details', $product->id));

        // Verificar que la vista se cargue correctamente con un estado 200
        $response->assertStatus(200);
    }

    /** @test */
    public function muestra_informacion_correcta_del_producto()
    {
        // Crear categoría y producto de prueba
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Producto de Prueba',
            'price' => 199.99,
            'description' => 'Este es un producto de prueba',
        ]);

        // Hacer una petición GET a la vista de detalles del producto
        $response = $this->get(route('products.details', $product->id));

        // Verificar que el nombre, precio y descripción del producto están presentes
        $response->assertSee('Producto de Prueba');
        $response->assertSee('199.99');
        $response->assertSee('Este es un producto de prueba');
        $response->assertSee($category->name);

        // Verificar si la imagen está presente si hay imágenes
        if ($product->images->isNotEmpty()) {
            $firstImage = $product->images->first()->image_path;
            $response->assertSee($firstImage);
        } else {
            // Verificar si la imagen por defecto está presente si no hay imágenes
            $response->assertSee(asset('img/default.png'));
        }
    }

    /** @test */
    public function muestra_botones_de_interaccion()
    {
        // Crear categoría y producto de prueba
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        // Hacer una petición GET a la vista de detalles del producto
        $response = $this->get(route('products.details', $product->id));

        // Verificar que existen los botones de agregar al carrito y volver al inicio
        $response->assertSee('Agregar al Carrito');
        $response->assertSee(route('home'));
    }
}
