@extends('admin.layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Gestión de Categorías</h2>
        <a href="{{ route('categories.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow">Crear Categoría</a>
    </div>

    <!-- Tabla de Categorías -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-3 px-4 border-b">Nombre</th>
                    <th class="py-3 px-4 border-b">Descripción</th>
                    <th class="py-3 px-4 border-b">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td class="py-3 px-4 border-b">{{ $category->name }}</td>
                        <td class="py-3 px-4 border-b">{{ $category->description }}</td>
                        <td class="py-3 px-4 border-b">
                            <a href="{{ route('categories.edit', $category->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded shadow hover:bg-yellow-600">Editar</a>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded shadow hover:bg-red-600">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
