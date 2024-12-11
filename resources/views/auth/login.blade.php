<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-300 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full bg-white shadow-xl rounded-lg p-8 space-y-6">
            <div class="text-center">
                <a href="/">
                    <img src="{{ asset('img/brand/fondourban.png') }}" alt="Logo" class="mx-auto h-16 w-auto">
                </a>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-800">Bienvenido de nuevo</h2>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form class="space-y-6 mx-auto" method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="max-w-lg mx-auto">
                    <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                        class="mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition ease-in-out duration-200" placeholder="Ingrese su correo electrónico">
                </div>

                <!-- Password Input -->
                <div class="max-w-lg mx-auto">
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition ease-in-out duration-200" placeholder="Ingrese su contraseña">
                </div>

                <!-- Login Button -->
                <div class="flex justify-center mt-6">
                    <button type="submit" class="w-36 py-3 px-6 text-base font-semibold text-white bg-green-500 rounded-lg shadow-md hover:bg-green-600 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-green-300 focus:ring-offset-2 transition-transform transform hover:scale-105 duration-300">
                        Iniciar Sesión
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-500">¿No tienes una cuenta?
                        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                            Registrate
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
