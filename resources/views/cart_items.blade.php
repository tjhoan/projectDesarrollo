@if ($cart && $cart->items->count() > 0)
    @foreach ($cart->items as $item)
        <div id="cart-item-{{ $item->id }}" class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <img src="{{ Str::startsWith($item->product->images->first()->image_path, 'http') ? $item->product->images->first()->image_path : Storage::url($item->product->images->first()->image_path) }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover mr-4">
                <div>
                    <h5 class="font-bold text-lg">{{ $item->product->name }}</h5>
                    <p class="text-gray-600">Cantidad: {{ $item->quantity }}</p>
                </div>
            </div>
            <button onclick="removeCartItem({{ $item->id }})" class="text-red-500 hover:text-red-700">Eliminar</button>
        </div>
    @endforeach

    <!-- Botón para vaciar el carrito -->
    <div class="flex justify-end mt-6 space-x-4">
        <button onclick="clearCart()" class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600 transition duration-300 ease-in-out">
            Vaciar Carrito
        </button>
        @if (Auth::check())
            <a href="{{ route('checkout') }}" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600 transition duration-300 ease-in-out">
                Finalizar Compra
            </a>
        @endif
    </div>
@else
    <p class="text-center text-gray-600">Tu carrito está vacío.</p>
@endif
