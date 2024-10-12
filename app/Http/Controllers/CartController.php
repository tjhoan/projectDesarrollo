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
        Log::info('Cargando carrito para el usuario o visitante...');

        // Primero buscamos por el token de la cookie si existe
        $token = request()->cookie('cart_token');
        $cart = null;

        if ($token) {
            $cart = Cart::with('items.product.images')
                ->where('token', $token)
                ->first();
            Log::info('Buscando carrito con token de cookie', ['token' => $token, 'cartId' => $cart ? $cart->id : null, 'itemsCount' => $cart ? $cart->items->count() : 0]);
        }

        // Si el usuario está autenticado y no tenemos un carrito de token, buscamos el del usuario
        if (Auth::check() && !$cart) {
            $cart = Cart::with('items.product.images')
                ->where('customer_id', Auth::id())
                ->first();
            Log::info('Buscando carrito para usuario autenticado', ['userId' => Auth::id(), 'cartId' => $cart ? $cart->id : null, 'itemsCount' => $cart ? $cart->items->count() : 0]);
        }

        // Si aún no tenemos un carrito, mostramos uno vacío
        if (!$cart) {
            Log::info('No se encontró ningún carrito para el usuario o visitante. Mostrando carrito vacío.');
        }

        return view('cart_items', compact('cart'));
    }

    public function add(Request $request, $productId)
    {
        Log::info('Añadiendo producto al carrito', ['productId' => $productId]);

        $cart = $this->getCart();
        Log::info('Carrito obtenido', ['cartId' => $cart->id]);

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

        // Devolvemos la respuesta con la cantidad de productos en el carrito
        $cartItemCount = $cart->items->sum('quantity');
        Log::info('Cantidad total de productos en el carrito', ['cartItemCount' => $cartItemCount]);

        return response()->json(['cartItemCount' => $cartItemCount]);
    }

    public function remove(Request $request, $itemId)
    {
        $cart = $this->getCart();

        if ($cart) {
            $cart->items()->where('id', $itemId)->delete();
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Producto eliminado del carrito', 'cartItemCount' => $cart->items->sum('quantity')]);
        }
        return redirect()->route('cart')->with('success', 'Producto eliminado del carrito');
    }

    public function clear()
    {
        $cart = Cart::where('token', request()->cookie('cart_token'))->orWhere('customer_id', Auth::id())->first();

        if ($cart) {
            $cart->items()->delete();
        }
        return redirect()->route('cart')->with('success', 'Carrito vaciado con éxitDo');
    }

    private function getCart()
    {
        // Obtener el token del carrito desde la cookie o generar un nuevo token si no existe
        $token = request()->cookie('cart_token') ?: Str::uuid();

        if (Auth::check()) {
            // Si el usuario está autenticado, buscamos o creamos un carrito para él
            $cart = Cart::firstOrCreate(['customer_id' => Auth::id()]);

            // Verificar si existe un carrito temporal para el visitante
            $visitorCart = Cart::where('token', $token)->whereNull('customer_id')->first();

            if ($visitorCart) {
                // Transferir los elementos del carrito temporal al carrito del usuario registrado
                foreach ($visitorCart->items as $item) {
                    // Verificar si el producto ya está en el carrito del usuario
                    $existingItem = $cart->items()->where('product_id', $item->product_id)->first();
                    if ($existingItem) {
                        // Incrementar la cantidad si el producto ya está en el carrito del usuario
                        $existingItem->quantity += $item->quantity;
                        $existingItem->save();
                    } else {
                        // Agregar el producto al carrito del usuario
                        $cartItem = new CartItem(['product_id' => $item->product_id, 'quantity' => $item->quantity]);
                        $cart->items()->save($cartItem);
                    }
                }

                // Eliminar el carrito temporal del visitante
                $visitorCart->items()->delete();
                $visitorCart->delete();
            }

            // Asegurarse de que el carrito ya no tenga un token de visitante
            $cart->customer_id = Auth::id();
            $cart->token = null;
            $cart->save();
        } else {
            // Si el usuario no está registrado, creamos o buscamos un carrito temporal por token
            $cart = Cart::firstOrCreate(['token' => $token]);
            cookie()->queue(cookie('cart_token', $token, 60 * 24 * 30)); // Guardamos el token del carrito en la cookie
        }

        // Asegurarse de que el carrito ha sido creado y tiene un ID válido
        if (!$cart->exists) {
            $cart->save();
        }

        return $cart;
    }
}
