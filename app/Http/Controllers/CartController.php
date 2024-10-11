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
        $cart = Cart::with('items.product.images')
            ->where('token', request()->cookie('cart_token'))
            ->orWhere('customer_id', Auth::id())
            ->first();

        return view('cart', compact('cart'));
    }

    public function add(Request $request)
    {
        // Validar el ID del producto
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Obtener o crear el carrito del usuario (o visitante)
        $cart = $this->getCart();

        // Obtener el producto a partir del ID
        $product = Product::findOrFail($request->input('product_id'));

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
        $cart = Cart::where('token', request()->cookie('cart_token'))->orWhere('customer_id', Auth::id())->first();

        if ($cart) {
            $cart->items()->where('id', $itemId)->delete();
        }

        return redirect()->route('cart')->with('success', 'Producto eliminado del carrito');
    }

    public function clear()
    {
        $cart = Cart::where('token', request()->cookie('cart_token'))->orWhere('customer_id', Auth::id())->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return redirect()->route('cart')->with('success', 'Carrito vaciado con éxito');
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
