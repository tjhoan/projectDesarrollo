@if ($cart && $cart->items->count() > 0)
    @foreach ($cart->items as $item)
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <img src="{{ asset($item->product->images->first()->image_path) }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover mr-4">
                <div>
                    <h5 class="font-bold text-lg">{{ $item->product->name }}</h5>
                    <p class="text-gray-600">Cantidad: {{ $item->quantity }}</p>
                </div>
            </div>
            <button onclick="removeCartItem({{ $item->id }})" class="text-red-500 hover:text-red-700">Eliminar</button>
        </div>
    @endforeach
@else
    <p class="text-center text-gray-600">Tu carrito está vacío.</p>
@endif
