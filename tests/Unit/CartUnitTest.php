<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Auth;

class CartUnitTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function carrito_esta_vacio_si_no_hay_productos()
  {
    $customer = Customer::factory()->create()->first();
    $this->actingAs($customer);

    $response = $this->get(route('cart'));
    $response->assertSee('Tu carrito está vacío');
  }

  /** @test */
  public function productos_del_carrito_se_muestran_correctamente()
  {
    // Crear un cliente (usuario)
    $customer = Customer::factory()->create()->first();
    $this->assertNotNull($customer);
    $this->actingAs($customer);

    // Crear un producto
    $product = Product::factory()->create();
    $this->assertNotNull($product);

    // Crear una imagen asociada al producto
    $image = ProductImage::create([
      'product_id' => $product->id,
      'image_path' => 'path/to/image.jpg'
    ]);

    // Agregar el producto al carrito
    $response = $this->post(route('cart.add', $product->id));

    // Verificar que el producto se ha agregado al carrito
    $response->assertStatus(200);
    $response->assertJson(['cartItemCount' => 1]);

    $response = $this->get(route('cart')); // Asegúrate de que la ruta 'cart' cargue el carrito

    // Verificar que el nombre del producto esté presente en la vista
    $response->assertSee($product->name);

    // Verificar que la imagen del producto esté en la vista
    $response->assertSee('path/to/image.jpg');
  }

  /** @test */
  public function usuario_ve_boton_de_finalizar_compra_cuando_autenticado()
  {
    $customer = Customer::factory()->create()->first();
    $this->actingAs($customer);

    $response = $this->get(route('cart'));
    $response->assertSee('Finalizar Compra');
  }

  /** @test */
  public function usuario_puede_eliminar_producto_del_carrito()
  {
    $customer = Customer::factory()->create()->first();
    $product = Product::factory()->create();
    $this->actingAs($customer);

    // Agregar el producto al carrito
    $this->post(route('cart.add', $product->id));

    // Eliminar el producto del carrito
    $response = $this->post(route('cart.remove', $product->id));

    // Verificar que el producto no está en el carrito
    $response = $this->get(route('cart'));
    $response->assertDontSee($product->name);
  }
}
