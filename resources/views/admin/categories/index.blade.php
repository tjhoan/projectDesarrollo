@extends('admin.layouts.admin')

@section('content')
    <style>
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
            min-width: 500px;
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

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold mt-4">Gestión de Categorías</h2>
        <a href="{{ route('categories.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow mr-4 mt-4">Crear Categoría</a>
    </div>

    <!-- Contenedor responsivo de la tabla de Categorías -->
    <div class="responsive-table-container">
        <table class="min-w-full bg-white table-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-3 px-5 text-left text-gray-600 font-semibold">Nombre</th>
                    <th class="py-3 px-5 text-left text-gray-600 font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody id="category-table-body">
                @foreach ($categories as $category)
                    <tr id="category-{{ $category->id }}" class="hover:bg-gray-100 transition duration-300 ease-in-out">
                        <td class="py-3 px-5 border-b">{{ $category->name }}</td>
                        <td class="py-3 px-5 border-b">
                            <button type="button" class="delete-category bg-red-500 text-white px-2 py-1 rounded shadow hover:bg-red-600" data-id="{{ $category->id }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Token CSRF para solicitudes AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar botones para eliminar categorías
            document.querySelectorAll('.delete-category').forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.getAttribute('data-id'); // ID de la categoría
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Token CSRF

                    // Mostrar alerta de confirmación con SweetAlert2
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "No podrás revertir esta acción",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Realizar la solicitud AJAX para eliminar la categoría
                            fetch(`/admin/categories/${categoryId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Eliminar la fila de la tabla si la respuesta es exitosa
                                        document.getElementById(`category-${categoryId}`).remove();

                                        Swal.fire(
                                            'Eliminado',
                                            'La categoría ha sido eliminada exitosamente.',
                                            'success'
                                        );
                                    } else {
                                        Swal.fire(
                                            'Error',
                                            'Ocurrió un error al eliminar la categoría.',
                                            'error'
                                        );
                                    }
                                })
                                .catch(error => {
                                    Swal.fire(
                                        'Error',
                                        'Ocurrió un error al eliminar la categoría.',
                                        'error'
                                    );
                                });
                        }
                    });
                });
            });
        });
    </script>
@endsection
