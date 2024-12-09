<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailsTest extends TestCase
{
  use RefreshDatabase; // Esto asegura que la base de datos se reinicie después de cada prueba

  /** @test */
  public function it_displays_product_details_correctly()
  {
    // Crear un producto en la base de datos
    $product = Product::factory()->create();

    // Hacer una solicitud GET a la ruta de detalles del producto usando la URL directamente
    $response = $this->get("/products/{$product->id}");

    // Verificar que la respuesta es exitosa (código 200)
    $response->assertStatus(200);

    // Verificar que los datos del producto se muestran correctamente en la vista
    $response->assertViewHas('product', $product);

    // Verificar que ciertos datos del producto están presentes en la vista
    $response->assertSee($product->name);
    $response->assertSee($product->price);

    // Si el producto tiene alguna imagen o categoría, también verificamos
    if ($product->images->isNotEmpty()) {
      $response->assertSee($product->images->first()->url);  // Asegura que la URL de la primera imagen se vea
    }

    $response->assertSee($product->category->name);  // Verifica que la categoría del producto esté en la vista
  }
}
