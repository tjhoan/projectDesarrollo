@extends('admin.layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 shadow-md rounded">
        <h2 class="text-2xl font-bold mb-4">Editar Administrador</h2>

        <form action="{{ route('admins.update', $admin->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700">Nombre</label>
                <input type="text" name="name" id="name" class="w-full border border-gray-300 px-3 py-2 rounded" value="{{ $admin->name }}" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="w-full border border-gray-300 px-3 py-2 rounded" value="{{ $admin->email }}" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700">Nueva Contraseña (opcional)</label>
                <input type="password" name="password" id="password" class="w-full border border-gray-300 px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700">Confirmar Nueva Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 px-3 py-2 rounded">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Actualizar</button>
        </form>
    </div>
@endsection
