@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-12 px-4 flex justify-center">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center w-full max-w-5xl">
            <!-- Imagen Principal con Carrusel -->
            <div class="relative">
                <div class="w-full h-96 flex justify-center bg-gray-100 p-4 relative">
                    <!-- Botón de navegación izquierda (más cerca de la imagen) -->
                    <div class="absolute left-2 inset-y-1/2 transform -translate-y-1/2">
                        <button class="bg-white rounded-full shadow-lg p-2 text-gray-600 hover:text-gray-900" id="prevImage">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Imagen principal del producto -->
                    @if ($product->images->isNotEmpty())
                        @php
                            $firstImagePath = $product->images->first()->image_path;
                            $firstImageUrl = Str::startsWith($firstImagePath, ['http', 'https']) ? $firstImagePath : Storage::url($firstImagePath);
                        @endphp
                        <img id="mainImage" src="{{ $firstImageUrl }}" alt="{{ $product->name }}" class="object-cover h-full w-auto">
                    @else
                        <img id="mainImage" src="{{ asset('img/default.png') }}" alt="Imagen no disponible" class="object-cover h-full w-auto">
                    @endif

                    <!-- Botón de navegación derecha (más cerca de la imagen) -->
                    <div class="absolute right-2 inset-y-1/2 transform -translate-y-1/2">
                        <button class="bg-white rounded-full shadow-lg p-2 text-gray-600 hover:text-gray-900" id="nextImage">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Carrusel de imágenes pequeñas debajo de la imagen principal -->
                <div class="flex space-x-2 mt-4 justify-center">
                    @foreach ($product->images as $index => $image)
                        @php
                            $imageUrl = Str::startsWith($image->image_path, ['http', 'https']) ? $image->image_path : Storage::url($image->image_path);
                        @endphp
                        <div class="w-20 h-20 border border-gray-300 cursor-pointer hover:border-indigo-500 transition" onclick="changeImage('{{ $imageUrl }}', {{ $index }})">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Detalles del Producto -->
            <div class="p-6 space-y-6 flex flex-col justify-center items-start">
                <!-- Nombre del producto con mejor tipografía -->
                <h1 class="text-4xl font-bold text-gray-900">{{ $product->name }}</h1>

                <!-- Precio destacado -->
                <div class="text-2xl text-indigo-600 font-semibold">
                    ${{ number_format($product->price, 2) }}
                </div>

                <!-- Información adicional con una jerarquía mejorada -->
                <div class="space-y-1">
                    <p class="text-gray-700 text-lg"><strong>Categoría:</strong> {{ $product->category->name }}</p>
                    <p class="text-gray-700 text-lg"><strong>Público Objetivo:</strong> {{ $product->target_audience }}</p>
                </div>

                <!-- Descripción del producto con estilo mejorado -->
                <p class="text-gray-600 text-base leading-relaxed">{{ $product->description }}</p>

                <!-- Botones de Agregar al Carrito y Volver al Inicio ajustados -->
                <div class="flex space-x-4 mt-6">
                    <!-- Botón de Agregar al Carrito con tamaño reducido -->
                    <button onclick="addToCart({{ $product->id }})" class="bg-indigo-500 text-white p-3 rounded-full shadow-lg hover:bg-indigo-600 transition transform hover:scale-105">
                        <img src="{{ asset('img/icons/cart.png') }}" alt="Carrito" class="w-6 h-6">
                    </button>

                    <!-- Botón de Volver al Inicio con tamaño reducido -->
                    <a href="{{ route('home') }}" class="bg-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-300 transition transform hover:scale-105">
                        <img src="{{ asset('img/icons/home.png') }}" alt="Home" class="w-6 h-6">
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentIndex = 0;
        const images = @json($product->images->pluck('image_path'));

        function changeImage(imageUrl, index) {
            document.getElementById('mainImage').src = imageUrl;
            currentIndex = index;
        }

        document.getElementById('prevImage').addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            document.getElementById('mainImage').src = images[currentIndex];
        });

        document.getElementById('nextImage').addEventListener('click', function() {
            currentIndex = (currentIndex + 1) % images.length;
            document.getElementById('mainImage').src = images[currentIndex];
        });
    </script>
@endpush
