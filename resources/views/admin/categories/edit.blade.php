@extends('admin.layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 shadow-md rounded">
        <h2 class="text-2xl font-bold mb-4">Editar Categoría</h2>

        <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700">Nombre de la Categoría</label>
                <input type="text" name="name" id="name" class="w-full border border-gray-300 px-3 py-2 rounded" value="{{ $category->name }}" required>
            </div>

            <div class="mb-4">
                <label for="subcategories" class="block text-gray-700">Subcategorías (separadas por comas)</label>
                <input type="text" name="subcategories[]" id="subcategories" class="w-full border border-gray-300 px-3 py-2 rounded" value="{{ implode(',', $category->subcategories->pluck('name')->toArray()) }}">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Actualizar</button>
        </form>
    </div>
@endsection
