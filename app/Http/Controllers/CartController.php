<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        return view('cart', compact('cart'));
    }

    public function add(Request $request, $productId)
    {
        try {
            $cart = $this->getCart();
            $product = Product::findOrFail($productId);

            // Verificar si el producto ya está en el carrito
            $cartItem = $cart->items()->where('product_id', $product->id)->first();

            if ($cartItem) {
                // Si ya está, aumentar la cantidad
                $cartItem->quantity += 1;
                $cartItem->save();
            } else {
                // Si no está, agregarlo al carrito
                $cartItem = new CartItem(['product_id' => $product->id, 'quantity' => 1]);
                $cart->items()->save($cartItem);
                Log::info('Productos en el carrito', ['items' => $cart->items]);
            }

            // Devolver la respuesta con la cantidad de productos en el carrito
            $cartItemCount = $cart->items->sum('quantity');
            return response()->json(['cartItemCount' => $cartItemCount]);
        } catch (\Exception $e) {
            Log::error('Error al agregar el producto al carrito', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error al agregar el producto: ' . $e->getMessage()], 500);
        }
    }

    public function remove(Request $request, $itemId)
    {
        $cart = $this->getCart();

        if ($cart) {
            $cart->items()->where('id', $itemId)->delete();
        }

        // Después de eliminar, recalcula el contador del carrito
        $cartItemCount = $cart ? $cart->items->sum('quantity') : 0;
        return response()->json(['cartItemCount' => $cartItemCount]);
    }

    public function clear()
    {
        $cart = $this->getCart();

        if ($cart) {
            $cart->items()->delete();
        }
        return redirect()->route('cart')->with('success', 'Carrito vaciado con éxito');
    }

    private function getCart()
    {
        if (Auth::check()) {
            // Usuario autenticado: buscar o crear un carrito para él
            $cart = Cart::firstOrCreate(['customer_id' => Auth::id()]);

            // Verificar si existe un carrito temporal para el visitante (usuario no autenticado)
            $token = request()->cookie('cart_token');

            if ($token) {
                $visitorCart = Cart::where('token', $token)->first();
                if ($visitorCart) {
                    // Cargar todos los productos del carrito del usuario autenticado para evitar múltiples consultas
                    $existingItems = $cart->items->keyBy('product_id');

                    // Transferir los elementos del carrito temporal al carrito del usuario registrado
                    foreach ($visitorCart->items as $item) {
                        if (isset($existingItems[$item->product_id])) {
                            // Incrementar la cantidad si el producto ya está en el carrito del usuario
                            $existingItems[$item->product_id]->quantity += $item->quantity;
                            $existingItems[$item->product_id]->save();
                        } else {
                            // Agregar el producto al carrito del usuario
                            $cartItem = new CartItem(['product_id' => $item->product_id, 'quantity' => $item->quantity]);
                            $cart->items()->save($cartItem);
                        }
                    }

                    // Eliminar el carrito temporal del visitante
                    $visitorCart->items()->delete();
                    $visitorCart->delete();

                    // Limpiar la cookie del carrito temporal
                    cookie()->queue(cookie()->forget('cart_token'));
                }
            }

            return $cart;
        } else {
            // Usuario no autenticado: buscar o crear un carrito temporal usando un token de cookie
            $token = request()->cookie('cart_token') ?: Str::uuid();
            $cart = Cart::firstOrCreate(['token' => $token]);

            // Guardar el token del carrito en una cookie si no existe
            cookie()->queue(cookie('cart_token', $token, 60 * 24 * 30)); // 30 días

            return $cart;
        }
    }
}
