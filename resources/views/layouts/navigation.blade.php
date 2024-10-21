<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-600" />
                    </a>
                </div>
            </div>
            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                <!-- Botón del Carrito de Compras en navigation.blade.php -->
                @if (request()->routeIs('home') || request()->routeIs('products.details'))
                    <a href="{{ route('cart') }}" class="relative flex items-center text-gray-500 hover:text-gray-700 transition">
                        <img src="{{ asset('img/icons/cartNav.png') }}" alt="Carrito" class="w-6 h-6">
                        @php
                            if (Auth::check()) {
                                // Si el usuario está autenticado, obtenemos su carrito por el ID del cliente
                                $cart = \App\Models\Cart::with('items')->where('customer_id', Auth::id())->first();
                            } else {
                                // Si no está autenticado, obtenemos el carrito temporal a través del token de la cookie
                                $cart = \App\Models\Cart::with('items')->where('token', request()->cookie('cart_token'))->first();
                            }
                            $cartItemCount = $cart ? $cart->items->sum('quantity') : 0;
                        @endphp
                        @if ($cartItemCount > 0)
                            <span class="cart-counter absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ $cartItemCount }}
                            </span>
                        @endif
                    </a>
                @endif
                @if (Auth::check())
                    <div class="flex items-center space-x-4">
                        <!-- Enlace directo para cerrar sesión con ícono -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition duration-150 ease-in-out">
                                <span class="material-icons">logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Registrarse</a>
                @endif
            </div>
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        @if (Auth::check())
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    {{ __('Cerrar sesión') }}
                </x-responsive-nav-link>
            </div>
        @else
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Iniciar Sesión') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Registrarse') }}
                </x-responsive-nav-link>
            </div>
        @endif
    </div>
</nav>
