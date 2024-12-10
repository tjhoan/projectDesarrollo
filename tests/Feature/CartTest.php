<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
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
}
