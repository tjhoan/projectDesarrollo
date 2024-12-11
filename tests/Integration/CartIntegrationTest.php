<?php

namespace Tests\Integration;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function muestra_la_vista_del_carrito()
    {
        $customer = Customer::factory()->create()->first();

        $this->actingAs($customer)
            ->get(route('cart'))
            ->assertStatus(200)
            ->assertViewIs('cart')
            ->assertViewHas('cart');
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
    public function puede_agregar_un_producto_al_carrito()
    {
        $customer = Customer::factory()->create()->first();
        $product = Product::factory()->create();

        $this->actingAs($customer)
            ->post(route('cart.add', $product->id))
            ->assertStatus(200)
            ->assertJson(['cartItemCount' => 1]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);
    }

    /** @test */
    public function puede_remover_un_producto_del_carrito()
    {
        $customer = Customer::factory()->create()->first();
        $cart = Cart::factory()->create(['customer_id' => $customer->id]);
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $this->actingAs($customer)
            ->delete(route('cart.remove', $cartItem->id))
            ->assertStatus(200)
            ->assertJson(['cartItemCount' => 0]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id
        ]);
    }

    /** @test */
    public function puede_vaciar_el_carrito()
    {
        $customer = Customer::factory()->create()->first();
        $cart = Cart::factory()->create(['customer_id' => $customer->id]);
        $product = Product::factory()->create();
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $this->actingAs($customer)
            ->delete(route('cart.clear'))
            ->assertRedirect(route('cart'))
            ->assertSessionHas('success', 'Carrito vaciado con éxito');

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id
        ]);
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
    public function prueba_barra_de_navegacion_para_usuario_no_autenticado()
    {
        $response = $this->get('/');
        $response->assertSee('Iniciar Sesión');
        $response->assertSee('Registrarse');
    }

    /** @test */
    public function carrito_esta_vacio_si_no_hay_productos()
    {
        $customer = Customer::factory()->create()->first();
        $this->actingAs($customer);

        $response = $this->get(route('cart'));
        $response->assertSee('Tu carrito está vacío');
    }
}
