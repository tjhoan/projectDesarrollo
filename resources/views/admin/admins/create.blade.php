@extends('admin.layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 shadow-md rounded">
        <h2 class="text-2xl font-bold mb-4">Crear Administrador</h2>

        <form id="admin-form" action="{{ route('admins.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-gray-700">Nombre</label>
                <input type="text" name="name" id="name" class="w-full border border-gray-300 px-3 py-2 rounded" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="w-full border border-gray-300 px-3 py-2 rounded" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700">Contraseña</label>
                <input type="password" name="password" id="password" class="w-full border border-gray-300 px-3 py-2 rounded" required>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 px-3 py-2 rounded" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
        </form>
    </div>

    <script>
        // SweetAlert para errores de validación
        @if ($errors->any())
            document.addEventListener("DOMContentLoaded", function() {
                let errorMessages = "";
                @foreach ($errors->all() as $error)
                    errorMessages += "{{ $error }}\n";
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMessages,
                    confirmButtonText: 'Aceptar'
                });
            });
        @endif
    </script>
@endsection
