@extends('admin.layouts.admin')

@section('content')
    <div class="main-content-wrapper bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-6">Crear Categoría</h2>

        <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nombre de la Categoría -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Nombre de la Categoría</label>
                <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" required>
            </div>

            <!-- Descripción de la Categoría -->
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Descripción de la Categoría</label>
                <textarea name="description" id="description" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" rows="4" placeholder="Descripción de la categoría"></textarea>
            </div>

            <!-- Subcategorías -->
            <div class="mb-4">
                <label for="subcategories" class="block text-gray-700 font-bold mb-2">Subcategorías (separadas por comas)</label>
                <input type="text" name="subcategories[]" id="subcategories" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200">
            </div>

            <!-- Botón de Guardar -->
            <div class="text-center">
                <button type="submit" class="w-full bg-gray-900 text-white py-2 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">Guardar</button>
            </div>
        </form>
    </div>
@endsection
