@extends('admin.layouts.admin')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <!-- Product Management -->
        <div class="bg-white shadow-md rounded-lg p-6 hover:shadow-lg transition duration-300">
            <h2 class="text-xl font-semibold mb-4">Gestión de Productos</h2>
            <a href="{{ route('products.index') }}" class="block bg-blue-500 text-white text-center py-2 rounded-lg shadow hover:bg-blue-600">
                Ver Productos
            </a>
        </div>

        <!-- Category Management -->
        <div class="bg-white shadow-md rounded-lg p-6 hover:shadow-lg transition duration-300">
            <h2 class="text-xl font-semibold mb-4">Gestión de Categorías</h2>
            <a href="{{ route('categories.index') }}" class="block bg-blue-500 text-white text-center py-2 rounded-lg shadow hover:bg-blue-600">
                Ver Categorías
            </a>
        </div>

        <!-- Admin Management -->
        <div class="bg-white shadow-md rounded-lg p-6 hover:shadow-lg transition duration-300">
            <h2 class="text-xl font-semibold mb-4">Gestión de Administradores</h2>
            <a href="{{ route('admins.index') }}" class="block bg-blue-500 text-white text-center py-2 rounded-lg shadow hover:bg-blue-600">
                Ver Administradores
            </a>
        </div>
    </div>
@endsection
