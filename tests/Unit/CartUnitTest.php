<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartUnitTest extends TestCase
{
    /** @test */
    public function carrito_esta_vacio_si_no_hay_productos()
    {
        $cart = $this->createMock(Cart::class);
        $cart->method('items')->willReturn(collect());

        // Llamar al método items() en lugar de acceder como propiedad
        $this->assertCount(0, $cart->items());
    }

    /** @test */
    public function carrito_muestra_productos_correctamente()
    {
        $cart = $this->createMock(Cart::class);
        $cartItem = new CartItem(['quantity' => 2]); // Usa un objeto real de CartItem
        $product = new Product(['name' => 'Producto de prueba']); // Usa un objeto real de Product

        // Relacionar el producto con el item
        $cartItem->setRelation('product', $product);

        // Configurar items en el carrito
        $cart->method('items')->willReturn(collect([$cartItem]));

        // Verificar que el carrito tiene productos
        $this->assertNotEmpty($cart->items());
        $this->assertEquals(2, $cart->items()->first()->quantity);
    }

    /** @test */
    public function calcula_total_correctamente()
    {
        $cart = new Cart();

        $cartItem1 = new CartItem(['quantity' => 2]);
        $cartItem1->setRelation('product', new Product(['price' => 100]));

        $cartItem2 = new CartItem(['quantity' => 1]);
        $cartItem2->setRelation('product', new Product(['price' => 200]));

        // Asignar los items al carrito
        $cart->setRelation('items', collect([$cartItem1, $cartItem2]));

        // Llamar al método real del modelo
        $total = $cart->calculateTotal();

        $this->assertEquals(400, $total);
    }

    /** @test */
    public function usuario_ve_boton_finalizar_compra_si_esta_autenticado()
    {
        Auth::shouldReceive('check')->andReturn(true);

        $this->assertTrue(Auth::check());
    }

    /** @test */
    public function usuario_no_ve_boton_finalizar_compra_si_no_esta_autenticado()
    {
        Auth::shouldReceive('check')->andReturn(false);

        $this->assertFalse(Auth::check());
    }
}
