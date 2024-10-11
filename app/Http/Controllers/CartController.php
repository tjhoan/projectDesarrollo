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
        // Utilizamos el método getCart() para obtener el carrito correcto (visitante o usuario registrado)
        $cart = $this->getCart();

        // Cargar los productos y sus imágenes asociados a los ítems del carrito
        $cart->load('items.product.images');

        // Retornamos la vista del carrito con los datos
        return view('cart', compact('cart'));
    }


    public function add(Request $request, $productId)
    {
        $cart = $this->getCart(); // Asegúrate de que siempre tenemos un carrito válido
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
        // Obtener el carrito actual (ya sea de visitante o usuario registrado)
        $cart = $this->getCart();

        if ($cart) {
            // Verificar si el ítem pertenece al carrito y eliminarlo
            $cartItem = $cart->items()->where('id', $itemId)->first();

            if ($cartItem) {
                $cartItem->delete();
            }
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
                $visitorCart->delete();
            }

            // Asegurarse de que el carrito esté asociado al usuario registrado
            $cart->customer_id = Auth::id();
            $cart->token = null; // Ya no necesitamos el token porque está asociado al cliente
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
