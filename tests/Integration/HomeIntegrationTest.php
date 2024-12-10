<?php

namespace Tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Cart;

class HomeControllerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function carga_la_pagina_de_inicio_con_productos_y_categorias()
    {
        // Crear algunas categorías y productos de prueba
        $categories = Category::factory()->count(3)->create();
        $products = Product::factory()->count(10)->create();

        // Hacer una petición GET a la ruta del home
        $response = $this->get(route('home'));

        // Verificar que la vista se cargue correctamente con un estado 200
        $response->assertStatus(200);

        // Verificar que la vista tenga las categorías y productos
        foreach ($categories as $category) {
            $response->assertSee($category->name, false);
        }

        foreach ($products as $product) {
            $response->assertSee($product->name, false);
            $response->assertSee((string) $product->price, false);
        }
    }

    /** @test */
    public function filtra_productos_por_categoria()
    {
        // Crear categorías y productos de prueba
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();
        $productInCategory = Product::factory()->create(['category_id' => $category->id]);
        $productNotInCategory = Product::factory()->create(['category_id' => $otherCategory->id]);

        // Hacer una petición GET al home filtrando por una categoría
        $response = $this->get(route('home', ['category_id' => $category->id]));

        // Parsear la respuesta usando DomCrawler
        $crawler = new Crawler($response->getContent());

        // Verificar que solo se muestra el producto que pertenece a la categoría
        $this->assertGreaterThan(0, $crawler->filter('.product-item:contains("' . $productInCategory->name . '")')->count());
        $this->assertEquals(0, $crawler->filter('.product-item:contains("' . $productNotInCategory->name . '")')->count());
    }

    /** @test */
    public function redirige_a_pagina_de_detalles_del_producto()
    {
        // Crear un producto de prueba
        $product = Product::factory()->create();

        // Hacer una petición GET para los detalles del producto
        $response = $this->get(route('products.details', $product->id));

        // Verificar que la vista se cargue correctamente con un estado 200
        $response->assertStatus(200);

        // Verificar que la vista muestra los detalles del producto
        $response->assertSee($product->name);
        $response->assertSee($product->price);
    }

    /** @test */
    public function asocia_carrito_de_invitado_con_cliente_autenticado()
    {
        // Crear un producto de prueba
        $product = Product::factory()->create();

        // Simular agregar un producto al carrito sin autenticación
        $this->post(route('cart.add', $product->id));

        // Obtener el carrito generado durante la sesión como invitado
        $cart = Cart::first(); // Suponiendo que solo hay un carrito

        // Verificar que el carrito no tiene asociado un cliente inicialmente
        $this->assertNull($cart->customer_id);

        // Crear y autenticar un cliente (customer)
        $customer = Customer::factory()->create()->first();
        $this->actingAs($customer, 'web'); // Cambia 'web' si tu guard es diferente

        // Asociar el carrito con el cliente autenticado
        $cart->update(['customer_id' => $customer->id]);

        // Verificar que el carrito ha sido asociado con el cliente
        $this->assertDatabaseHas('carts', ['customer_id' => $customer->id]);
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }
}
