<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class PaymentUnitTest extends TestCase
{
  use RefreshDatabase;

  public function test_usuario_debe_poder_procesar_pago_si_esta_autenticado()
  {
    // Crear un usuario ficticio y autenticarlo
    $customer = Customer::factory()->create()->first();

    // Autenticar al usuario
    $this->actingAs($customer);

    // Realizar la solicitud
    $response = $this->post(route('payment.process'), []);
    $response->assertStatus(302); // Verificar que la solicitud fue exitosa
  }

  public function test_payment_process_with_empty_cart()
  {
    $customer = Customer::factory()->create();
    Auth::login($customer);

    $response = $this->post(route('payment.process'), []);
    $response->assertRedirect();
    $response->assertSessionHas('error', 'No tienes productos en tu carrito.');
  }

  public function test_proceso_de_pago_exitoso()
  {
    // Crear un producto para que exista en la base de datos
    $product = Product::factory()->create();

    // Crear un cliente de prueba
    $customer = Customer::factory()->create();

    // Crear un carrito de compras asociado al cliente
    $cart = Cart::factory()->create(['customer_id' => $customer->id]);

    // Crear un item en el carrito con un producto existente
    $cart->items()->create([
      'product_id' => $product->id, // Usar el ID del producto creado
      'quantity' => 2,
    ]);

    // Iniciar sesión como el cliente creado
    Auth::login($customer);

    // Realizar una solicitud POST con datos válidos
    $response = $this->post(route('payment.process'), [
      'full_name' => 'John Doe',
      'address' => '123 Main St',
      'city' => 'Anytown',
      'state' => 'Anystate',
      'phone' => '1234567890',
      'payment_method' => 'daviplata',
      'pdf_invoice' => true,
      'email_invoice' => true,
    ]);

    // Verificar la respuesta
    $response->assertJson(['success' => true, 'message' => 'Pago procesado con éxito.']);

    // Verificar que la base de datos tiene la información correcta
    $this->assertDatabaseHas('payments', ['full_name' => 'John Doe']);
    $this->assertDatabaseHas('invoices', ['customer_id' => $customer->id]);
  }
  public function test_proceso_de_pago_genera_factura_pdf()
  {
    // Crear un producto en la base de datos
    $product = Product::factory()->create();

    // Crear un cliente de prueba
    $customer = Customer::factory()->create();

    // Crear un carrito de compras asociado al cliente
    $cart = Cart::factory()->create(['customer_id' => $customer->id]);

    // Agregar un producto al carrito
    $cart->items()->create([
      'product_id' => $product->id, // Usar el ID del producto creado
      'quantity' => 2,
    ]);

    // Iniciar sesión como el cliente creado
    Auth::login($customer);

    // Realizar una solicitud POST con datos válidos
    $response = $this->post(route('payment.process'), [
      'full_name' => 'John Doe',
      'address' => '123 Main St',
      'city' => 'Anytown',
      'state' => 'Anystate',
      'phone' => '1234567890',
      'payment_method' => 'daviplata',
      'pdf_invoice' => true,
      'email_invoice' => true,
    ]);

    // Verificar que el archivo PDF fue generado
    $invoice = Invoice::latest()->first();
    $pdfPath = storage_path('app/public/invoices/' . $invoice->invoice_number . '.pdf');
    $this->assertFileExists($pdfPath);
  }

  public function test_proceso_de_pago_muestra_mensaje_de_error_si_no_hay_productos_en_el_carrito()
  {
    $customer = Customer::factory()->create();
    Auth::login($customer);

    $response = $this->post(route('payment.process'), [
      'full_name' => 'John Doe',
      'address' => '123 Main St',
      'city' => 'Anytown',
      'state' => 'Anystate',
      'phone' => '1234567890',
      'payment_method' => 'daviplata',
    ]);

    $response->assertSessionHas('error', 'No tienes productos en tu carrito.');
  }
}
