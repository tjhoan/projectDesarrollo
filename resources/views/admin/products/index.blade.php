@extends('admin.layouts.admin')

@section('content')
    <style>
        /* Imagen del carrusel */
        .carousel-images {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }

        .carousel-container {
            display: flex;
            align-items: center;
            overflow-x: auto;
            scroll-behavior: smooth;
        }

        .carousel-container::-webkit-scrollbar {
            display: none;
        }

        .carousel-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .responsive-table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 6px;
            padding: 0.5rem;
        }

        .responsive-table-container::-webkit-scrollbar {
            height: 6px;
        }

        .responsive-table-container::-webkit-scrollbar-thumb {
            background: #9ca3af;
            border-radius: 10px;
        }

        .responsive-table-container::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        table {
            width: 100%;
            min-width: 700px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px;
            font-size: 14px;
            text-align: left;
            white-space: nowrap;
        }

        @media (max-width: 640px) {
            .responsive-table-container {
                padding: 0.25rem;
            }

            th,
            td {
                font-size: 12px;
                padding: 4px;
            }
        }
    </style>

    <div class="flex justify-between items-center mb-4 mt-2">
        <h2 class="text-2xl font-bold mt-4">Gestión de Productos</h2>
        <a href="{{ route('products.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow">Crear Producto</a>
    </div>

    <!-- Contenedor de la tabla con scroll horizontal -->
    <div class="responsive-table-container">
        <table class="min-w-full bg-white table-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-3 px-5 text-left text-gray-600 font-semibold">Imagen</th>
                    <th class="py-3 px-5 text-left text-gray-600 font-semibold">Nombre</th>
                    <th class="py-3 px-5 text-left text-gray-600 font-semibold">Precio</th>
                    <th class="py-3 px-5 text-left text-gray-600 font-semibold">Stock</th>
                    <th class="py-3 px-5 text-left text-gray-600 font-semibold">Categoría</th>
                    <th class="py-3 px-5 text-left text-gray-600 font-semibold">Marca</th>
                    <th class="py-3 px-5 text-left text-gray-600 font-semibold">Público Objetivo</th>
                    <th class="py-3 px-5 text-left text-gray-600 font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="hover:bg-gray-100 transition duration-300 ease-in-out">
                        <td class="py-3 px-5 border-b">
                            <div class="carousel-container">
                                @foreach ($product->images as $image)
                                    @php
                                        $imageUrl = Str::startsWith($image->image_path, 'http') ? $image->image_path : Storage::url($image->image_path);
                                    @endphp
                                    <img src="{{ $imageUrl }}" class="carousel-images cursor-pointer hover:scale-110 transition-transform duration-300" alt="Imagen del producto">
                                @endforeach
                            </div>
                        </td>
                        <td class="py-3 px-5 border-b">{{ $product->name }}</td>
                        <td class="py-3 px-5 border-b">{{ $product->price }}</td>
                        <td class="py-3 px-5 border-b">{{ $product->quantity }}</td>
                        <td class="py-3 px-5 border-b">{{ $product->category->name }}</td>
                        <td class="py-3 px-5 border-b">{{ $product->brand }}</td>
                        <td class="py-3 px-5 border-b">{{ ucfirst($product->target_audience) }}</td>
                        <td class="py-3 px-5 border-b">
                            <button type="button" class="bg-red-500 text-white px-3 py-2 rounded shadow hover:bg-red-600">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
