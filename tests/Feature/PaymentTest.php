<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_generacion_de_factura_pdf()
    {
        $customer = Customer::factory()->create()->first();

        // Crear un carrito con productos
        $cart = Cart::factory()->create(['customer_id' => $customer->id]);
        $product = Product::factory()->create();
        CartItem::factory()->create([
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
        $response->assertJson(['success' => true]);

        // Verificar que el archivo PDF existe en el almacenamiento
        $invoice = Invoice::where('customer_id', $customer->id)->first();
        $pdfPath = storage_path('app/public/invoices/' . $invoice->invoice_number . '.pdf');
        $this->assertFileExists($pdfPath);
    }

    /** @test */
    public function test_guarda_un_pago_en_la_base_de_datos()
    {
        // Crear un cliente autenticado
        $customer = Customer::factory()->create()->first();

        // Simular autenticación
        $this->actingAs($customer);

        // Crear un pago en la base de datos
        $paymentData = [
            'customer_id' => $customer->id,
            'full_name' => 'John Doe',
            'address' => '123 Main St',
            'city' => 'Springfield',
            'state' => 'Centro',
            'phone' => '555-1234',
            'payment_method' => 'daviplata',
            'pdf_invoice' => true,
        ];

        Payment::create($paymentData);

        // Verificar que el pago fue guardado en la base de datos
        $this->assertDatabaseHas('payments', $paymentData);
    }

    /** @test */
    public function test_carrito_es_vaciado_despues_de_pago_exitoso()
    {
        // Crear un cliente autenticado
        $customer = Customer::factory()->create()->first();

        // Crear un carrito con productos
        $cart = Cart::factory()->create(['customer_id' => $customer->id]);
        $product = Product::factory()->create(['price' => 100]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        // Simular autenticación del cliente
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

        // Verificar que el pago fue guardado en la base de datos
        $this->assertDatabaseHas('payments', [
            'customer_id' => $customer->id,
            'full_name' => 'John Doe',
            'payment_method' => 'daviplata',
        ]);

        // Verificar que el carrito fue vaciado
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
        ]);
    }

    /** @test */
    public function test_se_muestra_qr_correcto_segun_metodo_de_pago()
    {
        // Crear un cliente autenticado
        $customer = Customer::factory()->create()->first();

        // Simular autenticación
        $this->actingAs($customer);

        // Métodos de pago con sus respectivas rutas de imágenes QR
        $metodosDePago = [
            'daviplata' => asset('img/QR/QRDaviPlata.jpg'),
            'bancolombia' => asset('img/QR/QRBancolombiaALaMano.jpg')
        ];

        foreach ($metodosDePago as $metodo => $rutaQr) {
            // Simular el formulario de pago con datos válidos
            $response = $this->post(route('payment.process'), [
                'full_name' => 'John Doe',
                'address' => '123 Main St',
                'city' => 'Springfield',
                'state' => 'Centro',
                'phone' => '555-1234',
                'payment_method' => $metodo,
                'pdf_invoice' => true,
            ]);

            // Verificar si el usuario está autenticado correctamente
            $this->assertAuthenticated();

            // Verificar que el pago fue procesado correctamente, deberia redirigir a la vista home
            $response->assertStatus(302);

            // Simular la lógica que muestra el QR en la vista
            $response = $this->get(route('checkout'));  // Vista donde se muestra el QR
            $response->assertStatus(200);  // Verificar que la vista carga correctamente
            $response->assertSee($rutaQr);  // Verificar que el QR correcto aparece en la vista
        }
    }
}
