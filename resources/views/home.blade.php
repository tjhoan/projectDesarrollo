@extends('layouts.app')
@section('content')
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <div class="container mx-auto my-6 px-4 overflow-y-hidden">
        <!-- Carrusel de Categorías -->
        <div class="mb-6">
            <div class="overflow-x-auto whitespace-nowrap flex items-center gap-4 no-scrollbar">
                <button class="flex-shrink-0 border border-gray-300 text-gray-800 py-2 px-4 rounded-full shadow hover:bg-gray-200 transition duration-300 ease-in-out transform hover:scale-105" onclick="loadProducts(null)">
                    <h3 class="text-base font-medium">Todas las categorías</h3>
                </button>
                @foreach ($categories as $category)
                    <button class="flex-shrink-0 border border-gray-300 text-gray-800 py-2 px-4 rounded-full shadow hover:bg-gray-200 transition duration-300 ease-in-out transform hover:scale-105" onclick="loadProducts({{ $category->id }})">
                        <h3 class="text-base font-medium">{{ $category->name }}</h3>
                    </button>
                @endforeach
            </div>
        </div>
        <!-- Productos -->
        <div id="products-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach ($products as $product)
                <div class="product-item bg-white rounded-xl shadow-md overflow-hidden flex flex-col transition-transform transform hover:scale-105 duration-500 ease-in-out">
                    <!-- Contenedor de Imagen Ajustado -->
                    <div class="w-full h-58 overflow-hidden flex items-center justify-center bg-gray-100">
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
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <h5 class="text-lg font-bold text-gray-900 mb-2">{{ $product->name }}</h5>
                        <p class="text-base text-gray-700 font-semibold mb-4">Precio: ${{ $product->price }}</p>
                        <div class="flex justify-between items-center mt-auto">
                            <a href="{{ route('products.details', $product->id) }}" class="bg-blue-500 text-white py-2 px-3 rounded-lg shadow hover:bg-blue-600 transition duration-300 ease-in-out transform hover:scale-105">
                                Ver Detalles
                            </a>
                            <button onclick="addToCart({{ $product->id }})" class="bg-white text-indigo-500 p-2 rounded-lg hover:bg-indigo-100 transition flex items-center">
                                <img src="{{ asset('img/icons/cart.png') }}" alt="Carrito" class="w-6 h-6">
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentPage = 1;

        // Cargar productos de acuerdo a la categoría seleccionada
        function loadProducts(categoryId) {
            currentPage = 1; // Resetear el contador de página
            $.ajax({
                url: "{{ route('home') }}",
                type: "GET",
                data: {
                    category_id: categoryId,
                    page: currentPage
                },
                success: function(response) {
                    $('#products-container').html(response.html); // Actualiza la lista de productos
                }
            });
        }

        // Función para cargar más productos al hacer scroll
        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
                currentPage++; // Incrementar la página
                $.ajax({
                    url: "{{ route('home') }}",
                    type: "GET",
                    data: {
                        page: currentPage
                    },
                    success: function(response) {
                        $('#products-container').append(response.html); // Añadir productos
                    }
                });
            }
        });
    </script>
@endpush
