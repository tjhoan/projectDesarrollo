<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_processes_the_payment_and_creates_an_invoice()
    {
        // Crear un cliente autenticado
        $customer = Customer::factory()->create();

        // Crear un carrito con productos
        $cart = Cart::factory()->create(['customer_id' => $customer->id]);
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        // Simular autenticación
        $this->actingAs($customer);

        // Enviar el formulario de pago
        $response = $this->post(route('payment.process'), [
            'full_name' => 'John Doe',
            'address' => '123 Main St',
            'city' => 'Springfield',
            'state' => 'Centro',
            'phone' => '555-1234',
            'payment_method' => 'daviplata',
            'pdf_invoice' => true,
        ]);

        // Asegurar que el pago fue procesado correctamente
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verificar que el pago fue creado en la base de datos
        $this->assertDatabaseHas('payments', [
            'customer_id' => $customer->id,
            'full_name' => 'John Doe',
            'payment_method' => 'daviplata',
        ]);

        // Verificar que la factura fue creada
        $this->assertDatabaseHas('invoices', [
            'customer_id' => $customer->id,
            'total_amount' => $cart->calculateTotal(),
        ]);

        // Verificar que el carrito fue vaciado
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id
        ]);
    }

    /** @test */
    public function it_validates_the_payment_form()
    {
        // Crear un cliente autenticado
        $customer = Customer::factory()->create();

        // Simular autenticación
        $this->actingAs($customer);

        // Enviar el formulario de pago con datos incompletos
        $response = $this->post(route('payment.process'), [
            'full_name' => '', // Campo vacío
            'address' => '123 Main St',
            'city' => 'Springfield',
            'state' => 'Centro',
            'phone' => '555-1234',
            'payment_method' => 'daviplata',
        ]);

        // Asegurar que la validación falla
        $response->assertSessionHasErrors('full_name');
    }
}
