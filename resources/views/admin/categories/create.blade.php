@extends('admin.layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 shadow-md rounded">
        <h2 class="text-2xl font-bold mb-4">Crear Categoría</h2>

        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-gray-700">Nombre de la Categoría</label>
                <input type="text" name="name" id="name" class="w-full border border-gray-300 px-3 py-2 rounded" required>
            </div>

            <div class="mb-4">
                <label for="subcategories" class="block text-gray-700">Subcategorías (separadas por comas)</label>
                <input type="text" name="subcategories[]" id="subcategories" class="w-full border border-gray-300 px-3 py-2 rounded">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
        </form>
    </div>
@endsection
