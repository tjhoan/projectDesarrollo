@extends('admin.layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold mt-4" style="margin-left: 50px">Gestión de Productos</h2>
        <a href="{{ route('products.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow mr-4 mt-4">Crear Producto</a>
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full bg-white table-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-4 px-6 text-left text-gray-600 font-semibold">Imagen</th>
                    <th class="py-4 px-6 text-left text-gray-600 font-semibold">Nombre</th>
                    <th class="py-4 px-6 text-left text-gray-600 font-semibold">Precio</th>
                    <th class="py-4 px-6 text-left text-gray-600 font-semibold">Stock</th>
                    <th class="py-4 px-6 text-left text-gray-600 font-semibold">Categoría</th>
                    <th class="py-4 px-6 text-left text-gray-600 font-semibold">Subcategoría</th>
                    <th class="py-4 px-6 text-left text-gray-600 font-semibold">Marca</th>
                    <th class="py-4 px-6 text-left text-gray-600 font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="hover:bg-gray-100 transition duration-300 ease-in-out" id="product-{{ $product->id }}">
                        <!-- Mostrar imagen -->
                        <td class="py-4 px-6 border-b">
                            @foreach ($product->images as $image)
                                @php
                                    $imageUrl = Str::startsWith($image->image_path, 'http') ? $image->image_path : Storage::url($image->image_path);
                                @endphp
                                <img src="{{ $imageUrl }}" class="w-16 h-16 object-cover rounded-lg hover:scale-105 transition-transform" alt="Imagen del producto">
                            @endforeach
                        </td>
                        <td class="py-4 px-6 border-b">{{ $product->name }}</td>
                        <td class="py-4 px-6 border-b">{{ $product->price }}</td>
                        <td class="py-4 px-6 border-b">{{ $product->quantity }}</td>
                        <td class="py-4 px-6 border-b">{{ $product->category->name }}</td>
                        <td class="py-4 px-6 border-b">{{ $product->subcategory->name ?? 'N/A' }}</td>
                        <td class="py-4 px-6 border-b">{{ $product->brand }}</td>

                        <td class="py-4 px-6 border-b">
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-2 rounded shadow hover:bg-red-600">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
