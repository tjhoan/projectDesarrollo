<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-300 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full bg-white shadow-xl rounded-lg p-8 space-y-6">
            <div class="text-center">
                <a href="/">
                    <img src="{{ asset('img/brand/fondourban.png') }}" alt="Logo" class="mx-auto h-16 w-auto">
                </a>
                <h2 class="mt-4 text-3xl font-extrabold text-gray-800">Crear una cuenta</h2>
                <p class="mt-1 text-sm text-gray-600">Regístrate para comenzar</p>
            </div>

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input id="first_name" class="mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition ease-in-out duration-200" type="text" name="first_name"
                        value="{{ old('first_name') }}" required autofocus>
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Apellido</label>
                    <input id="last_name" class="mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition ease-in-out duration-200" type="text" name="last_name"
                        value="{{ old('last_name') }}" required>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <input id="email" class="mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition ease-in-out duration-200" type="email" name="email"
                        value="{{ old('email') }}" required>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input id="password" class="mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition ease-in-out duration-200" type="password" name="password" required>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                    <input id="password_confirmation" class="mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition ease-in-out duration-200" type="password"
                        name="password_confirmation" required>
                </div>

                <!-- Register Button -->
                <div class="flex justify-center mt-6">
                    <button type="submit"
                        class="w-36 py-3 px-6 text-base font-semibold text-white bg-green-500 rounded-lg shadow-md hover:bg-green-600 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-green-300 focus:ring-offset-2 transition-transform transform hover:scale-105 duration-300">
                        Registrarse
                    </button>
                </div>
            </form>

            <div class="text-center">
                <p class="text-sm text-gray-500">¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                        Inicia Sesión
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
