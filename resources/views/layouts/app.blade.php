<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/home.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <!-- Cargar jQuery desde el CDN -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8= sha256-T+aPohYXbm0fRYDpJLr+zJ9RmYTswGsahAoIsNiMld4=" crossorigin="anonymous"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif
        <main>
            @yield('content')
        </main>
        <div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
            <div id="cartModalContent" class="bg-white rounded-lg w-full max-w-lg p-6 relative">
                <button onclick="closeCartModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                <h2 class="text-2xl font-bold mb-4">Tu Carrito de Compras</h2>
                <div id="cartItemsContainer">
                    <!-- Aquí se cargarán los artículos del carrito -->
                    <p class="text-center text-gray-600">Tu carrito está vacío.</p>
                </div>
                <button onclick="finalizePurchase()" class="mt-4 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600">Finalizar Compra</button>
            </div>
        </div>
        <script>
            function addToCart(productId) {
                $.ajax({
                    url: "{{ url('/cart/add') }}/" + productId,
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        // Actualizamos el número de artículos en el carrito
                        $('.cart-counter').text(response.cartItemCount);
                        $('.cart-counter').show(); 
                        alertify.success('Producto añadido al carrito');
                        // Actualizar el contenido del carrito en el modal si es necesario
                        loadCartItems();
                    },
                    error: function(error) {
                        console.error("Error al agregar al carrito:", error);
                        alertify.error('Hubo un problema al agregar el producto al carrito.');
                    }
                });
            }

            function openCartModal() {
                document.getElementById('cartModal').classList.remove('hidden'); // Mostrar el modal
            }

            function closeCartModal() {
                document.getElementById('cartModal').classList.add('hidden'); // Ocultar el modal
            }

            // Cerrar el modal al hacer clic fuera del contenido del modal
            document.getElementById('cartModal').addEventListener('click', function(event) {
                if (event.target.id === 'cartModal') {
                    closeCartModal();
                }
            });

            function loadCartItems() {
                // Cargar los artículos del carrito
                $.ajax({
                    url: "{{ route('cart') }}",
                    method: "GET",
                    success: function(response) {
                        $('#cartItemsContainer').html(response);
                        updateCartCounter();
                    },
                    error: function(error) {
                        console.error("Error al cargar los artículos del carrito:", error);
                    }
                });
            }

            function updateCartCounter() {
                // Actualizar el contador del carrito
                $.ajax({
                    url: "{{ route('cart') }}",
                    method: "GET",
                    success: function(response) {
                        let itemCount = $(response).find('.cart-item').length;
                        $('.cart-counter').text(itemCount);
                        if (itemCount > 0) {
                            $('.cart-counter').show();
                        } else {
                            $('.cart-counter').hide();
                        }
                    },
                    error: function(error) {
                        console.error("Error al actualizar el contador del carrito:", error);
                    }
                });
            }

            function removeCartItem(itemId) {
                // Confirmar si el usuario desea eliminar el artículo del carrito
                $.ajax({
                    url: "{{ url('/cart/remove') }}/" + itemId,
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $('.cart-counter').text(response.cartItemCount);
                        alertify.error('Producto eliminado del carrito');
                        loadCartItems();
                    },
                    error: function(error) {
                        console.error("Error al eliminar el artículo del carrito:", error);
                        alertify.error('Hubo un problema al eliminar el artículo del carrito.');
                    }
                });
            }
        </script>
    </div>
</body>
</html>
