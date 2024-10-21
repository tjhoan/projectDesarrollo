@foreach ($products as $product)
    <div class="product-item bg-white rounded-xl shadow-md overflow-hidden transition-transform transform hover:scale-105 duration-500 ease-in-out">
        <div class="w-full h-64 overflow-hidden flex items-center justify-center bg-gray-100">
            @if ($product->images->isNotEmpty())
                @php
                    $image = $product->images->first();
                    $imageUrl = Str::startsWith($image->image_path, 'http') ? $image->image_path : Storage::url($image->image_path);
                @endphp
                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
            @else
                <img src="{{ asset('img/default.png') }}" alt="Imagen no disponible" class="object-cover w-full h-full">
            @endif
        </div>
        <div class="p-6">
            <h5 class="text-lg font-bold text-gray-900 mb-2">{{ $product->name }}</h5>
            <p class="text-base text-gray-700 font-semibold mb-4">Precio: ${{ $product->price }}</p>
            <div class="flex justify-between items-center">
                <a href="{{ route('products.show', $product->id) }}" class="bg-blue-500 text-white py-2 px-3 rounded-lg shadow hover:bg-blue-600 transition duration-300 ease-in-out transform hover:scale-105">
                    Ver Detalles
                </a>
                <button onclick="addToCart({{ $product->id }})" class="bg-white text-indigo-500 p-2 rounded-lg hover:bg-indigo-100 transition flex items-center">
                    <img src="{{ asset('img/icons/cart.png') }}" alt="Carrito" class="w-6 h-6">
                </button>
            </div>
        </div>
    </div>
@endforeach
