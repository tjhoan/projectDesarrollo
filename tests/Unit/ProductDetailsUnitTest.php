<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;

class ProductDetailsUnitTest extends TestCase
{
  use RefreshDatabase; // Esto asegura que la base de datos se reinicie después de cada prueba

  /** @test */
  public function test_muestra_los_detalles_del_producto_correctamente()
  {
    // Crear un producto en la base de datos
    $product = Product::factory()->create();

    // Hacer una solicitud GET a la ruta de detalles del producto usando la URL directamente
    $response = $this->get(route('products.details', $product->id));


    // Verificar que la respuesta es exitosa (código 200)
    $response->assertStatus(200);

    // Verificar que los datos del producto se muestran correctamente en la vista
    $response->assertViewHas('product', $product);

    // Verificar que ciertos datos del producto están presentes en la vista
    $response->assertSee($product->name);
    $response->assertSee($product->price);
    $response->assertSee($product->description);

    // Si el producto tiene alguna imagen o categoría, también verificamos
    if ($product->images->isNotEmpty()) {
      $response->assertSee($product->images->first()->url);  // Asegura que la URL de la primera imagen se vea
    }

    $response->assertSee($product->category->name);  // Verifica que la categoría del producto esté en la vista
  }
}
