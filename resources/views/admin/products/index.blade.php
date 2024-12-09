@extends('admin.layouts.admin')

@section('content')
    <style>
        /* Estilos para la tabla y carrusel de imágenes */
        .carousel-images {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .carousel-container {
            display: flex;
            align-items: center;
            overflow-x: auto;
            scroll-behavior: smooth;
            max-height: 80px;
        }

        .carousel-container::-webkit-scrollbar {
            display: none;
        }

        .carousel-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold mt-4" style="margin-left: 50px">Gestión de Productos</h2>
        <a href="{{ route('products.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow mr-4 mt-4">Crear Producto</a>
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
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
                    <tr class="hover:bg-gray-100 transition duration-300 ease-in-out" id="product-{{ $product->id }}">
                        <td class="py-3 px-5 border-b">
                            <div class="carousel-container scrollbar-custom">
                                @foreach ($product->images as $image)
                                    @php
                                        $imageUrl = Str::startsWith($image->image_path, 'http') ? $image->image_path : Storage::url($image->image_path);
                                    @endphp
                                    <img src="{{ $imageUrl }}" class="carousel-images cursor-pointer hover:scale-125 transition-transform duration-300" alt="Imagen del producto">
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
                            <button type="button" class="bg-red-500 text-white px-3 py-2 rounded shadow hover:bg-red-600 delete-btn" data-id="{{ $product->id }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        // Función para eliminar un producto usando AJAX
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                // Confirmación de eliminación
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'No podrás revertir esto una vez eliminado.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminarlo',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, realizamos la petición DELETE
                        fetch(`/admin/products/${productId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            }
                        }).then(response => {
                            if (response.ok) {
                                // Eliminamos la fila del producto y mostramos un mensaje de éxito con SweetAlert
                                document.getElementById(`product-${productId}`).remove();
                                Swal.fire(
                                    'Eliminado',
                                    'El producto ha sido eliminado correctamente.',
                                    'success'
                                );
                            } else {
                                // Mensaje de error si algo sale mal
                                Swal.fire(
                                    'Error',
                                    'Hubo un problema al eliminar el producto.',
                                    'error'
                                );
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error',
                                'Ocurrió un error inesperado al procesar la solicitud.',
                                'error'
                            );
                        });
                    }
                });
            });
        });
    </script>
@endsection
