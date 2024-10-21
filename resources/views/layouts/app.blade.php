<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <script>
            $(document).ready(function() {
                // Configuración global para incluir el token CSRF en las solicitudes AJAX
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });

            function addToCart(productId) {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: "{{ url('/cart/add') }}/" + productId,
                    method: "POST",
                    data: {
                        _token: csrfToken,
                    },
                    success: function(response) {
                        $('.cart-counter').text(response.cartItemCount);
                        $('.cart-counter').show();
                        alertify.success('Producto añadido al carrito');
                        loadCartItems();
                    },
                    error: function(error) {
                        alertify.error('Hubo un problema al agregar el producto al carrito.');
                    }
                });
            }

            function openCartModal() {
                $('#cartModal').removeClass('hidden');
                loadCartItems();
            }

            function closeCartModal() {
                $('#cartModal').addClass('hidden');
            }

            function loadCartItems() {
                $.ajax({
                    url: "{{ route('cart') }}",
                    method: "GET",
                    success: function(response) {
                        $('#cartItemsContainer').html(response);
                    },
                    error: function(error) {
                        console.error("Error al cargar los artículos del carrito:", error);
                    }
                });
            }

            function updateCartCounter() {
                $.ajax({
                    url: "{{ route('cart') }}",
                    method: "GET",
                    success: function(response) {
                        let itemCount = $(response).find('.cart-item').length;
                        if (itemCount > 0) {
                            $('.cart-counter').text(itemCount);
                            $('.cart-counter').show();
                        } else {
                            $('.cart-counter').text(0);
                            $('.cart-counter').hide();
                        }
                    },
                    error: function(error) {
                        console.error("Error al actualizar el contador del carrito:", error);
                    }
                });
            }

            function removeCartItem(itemId) {
                $.ajax({
                    url: "{{ url('/cart/remove') }}/" + itemId,
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "DELETE"
                    },
                    success: function(response) {
                        $('#cart-item-' + itemId).remove();
                        alertify.error('Producto eliminado del carrito');
                        $('.cart-counter').text(response.cartItemCount);
                        if (response.cartItemCount > 0) {
                            $('.cart-counter').show();
                        } else {
                            $('.cart-counter').hide();
                        }
                    },
                    error: function(error) {
                        alertify.error('Hubo un problema al eliminar el artículo del carrito.');
                    }
                });
            }
        </script>

        <!-- Modal del Carrito de Compras -->
        <div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
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
    </div>

    @stack('scripts')
</body>

</html>
