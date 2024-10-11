<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, $productId)
    {
        $cart = $this->getCart();
        $product = Product::findOrFail($productId);

        // Buscar si el producto ya está en el carrito
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Si ya está, aumentar la cantidad
            $cartItem->quantity += 1;
        } else {
            // Si no está, agregarlo al carrito
            $cartItem = new CartItem(['product_id' => $product->id, 'quantity' => 1]);
            $cart->items()->save($cartItem);
        }

        $cartItem->save();

        return redirect()->back()->with('success', 'Producto añadido al carrito');
    }

    public function remove($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        $cartItem->delete();

        return redirect()->back()->with('success', 'Producto eliminado del carrito');
    }

    public function clear()
    {
        $cart = $this->getCart();
        $cart->items()->delete();

        return redirect()->back()->with('success', 'Carrito vaciado');
    }

    private function getCart()
    {
        if (Auth::check()) {
            // Obtener el carrito del usuario registrado
            $cart = Cart::firstOrCreate(['customer_id' => Auth::id()]);
        } else {
            // Crear o recuperar el carrito del visitante usando un token
            $token = request()->cookie('cart_token') ?: Str::uuid();
            $cart = Cart::firstOrCreate(['token' => $token]);
            cookie()->queue(cookie('cart_token', $token, 60 * 24 * 30));
        }

        return $cart;
    }
}
