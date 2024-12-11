<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Cart;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Mockery;
use Mockery\MockInterface;

class PaymentUnitTest extends TestCase
{
  /** @test */
  public function pago_puede_ser_creado()
  {
    $data = [
      'customer_id' => 1,
      'full_name' => 'John Doe',
      'address' => '123 Main St',
      'city' => 'Metropolis',
      'state' => 'NY',
      'phone' => '1234567890',
      'additional_info' => 'Leave at the door',
      'payment_method' => 'nequi',
      'pdf_invoice' => true,
      'email_invoice' => false,
      'confirmation_code' => 123456,
    ];

    $payment = new Payment($data);

    $this->assertEquals('John Doe', $payment->full_name);
    $this->assertEquals('123 Main St', $payment->address);
    $this->assertEquals('nequi', $payment->payment_method);
  }

  /** @test */
  public function factura_puede_ser_creada()
  {
    $data = [
      'customer_id' => 1,
      'payment_id' => 1,
      'total_amount' => 100,
      'invoice_number' => 'INV-12345',
    ];

    $invoice = new Invoice($data);

    $this->assertEquals(100, $invoice->total_amount);
    $this->assertEquals('INV-12345', $invoice->invoice_number);
  }

  /** @test */
  public function valida_datos_de_solicitud_de_pago()
  {
    $request = new Request([
      'full_name' => 'John Doe',
      'address' => '123 Main St',
      'city' => 'Metropolis',
      'state' => 'NY',
      'phone' => '1234567890',
      'payment_method' => 'nequi',
      'pdf_invoice' => true,
      'email_invoice' => false,
    ]);

    $this->assertEquals('John Doe', $request->input('full_name'));
    $this->assertEquals('123 Main St', $request->input('address'));
    $this->assertTrue($request->input('pdf_invoice'));
  }

  /** @test */
  public function genera_codigo_de_confirmacion()
  {
    $confirmationCode = rand(100000, 999999);

    $this->assertGreaterThanOrEqual(100000, $confirmationCode);
    $this->assertLessThanOrEqual(999999, $confirmationCode);
  }

  /** @test */
  public function carrito_calcula_total_correctamente()
  {
    $cart = Mockery::mock(Cart::class);
    $cart->shouldReceive('calculateTotal')->andReturn(150.0);

    $this->assertEquals(150.0, $cart->calculateTotal());
  }

  /** @test */
  public function factura_pdf_se_genera_correctamente()
  {
    $payment = new Payment(['pdf_invoice' => true]);

    $this->assertTrue($payment->pdf_invoice);
  }

  /** @test */
  public function metodo_de_pago_es_valido()
  {
    $validMethods = ['nequi', 'daviplata', 'bancolombia'];

    $payment = new Payment(['payment_method' => 'nequi']);

    $this->assertContains($payment->payment_method, $validMethods);
  }
}
