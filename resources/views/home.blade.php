@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-10 px-4">
        <h1 class="text-4xl font-bold mb-10 text-center text-gray-800">Bienvenido al Home</h1>

        <!-- Carrusel de Categorías -->
        <div class="mb-16">
            <div class="overflow-x-auto whitespace-nowrap flex items-center gap-4">
                @foreach ($categories as $category)
                    <button class="flex-shrink-0 border border-gray-300 text-gray-800 py-2 px-4 rounded-full shadow hover:bg-gray-100 transition">
                        <h3 class="text-base">{{ $category->name }}</h3>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Productos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach ($products as $product)
                <div class="bg-white rounded-xl shadow-md overflow-hidden transition-transform transform hover:scale-105">
                    <!-- Contenedor de Imagen Ajustado -->
                    <div class="w-full h-64 overflow-hidden flex items-center justify-center bg-gray-100">
                        <img src="{{ asset($product->images->first()->image_path) }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                    </div>
                    <div class="p-6">
                        <h5 class="text-lg font-bold text-gray-900 mb-2">{{ $product->name }}</h5>
                        <p class="text-base text-gray-700 font-semibold mb-4">Precio: ${{ $product->price }}</p>
                        <div class="flex justify-between items-center">
                            <a href="{{ route('products.show', $product->id) }}" class="text-blue-500 font-semibold hover:underline">Ver Detalles</a>
                            <button class="bg-indigo-500 text-white p-2 rounded-lg hover:bg-indigo-600 transition flex items-center">
                                <img src="{{ asset('img/icons/cart.png') }}" alt="Carrito" class="w-5 h-5">
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
