<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <label for="first_name">Nombre</label>
                <input id="first_name" class="block mt-1 w-full" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus>
            </div>

            <div class="mt-4">
                <label for="last_name">Apellido</label>
                <input id="last_name" class="block mt-1 w-full" type="text" name="last_name" value="{{ old('last_name') }}" required>
            </div>

            <div class="mt-4">
                <label for="email">Correo Electrónico</label>
                <input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="mt-4">
                <label for="password">Contraseña</label>
                <input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password">
            </div>

            <div class="mt-4">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required>
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="submit" class="ml-4">
                    {{ __('Registrarse') }}
                </button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
