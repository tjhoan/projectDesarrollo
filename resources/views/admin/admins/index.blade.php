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

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold mt-4" style="margin-left: 10px">Gestión de Administradores</h2>
        <a href="{{ route('admins.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow mr-4 mt-4">Crear Administrador</a>
    </div>

    <!-- Contenedor responsivo de la tabla de Administradores -->
    <div class="responsive-table-container">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-3 px-5 border-b text-left text-gray-600 font-semibold">Nombre</th>
                    <th class="py-3 px-5 border-b text-left text-gray-600 font-semibold">Correo Electrónico</th>
                    <th class="py-3 px-5 border-b text-left text-gray-600 font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                    <tr id="admin-{{ $admin->id }}" class="hover:bg-gray-100 transition duration-300 ease-in-out">
                        <td class="py-3 px-5 border-b">{{ $admin->name }}</td>
                        <td class="py-3 px-5 border-b">{{ $admin->email }}</td>
                        <td class="py-3 px-5 border-b">
                            <button type="button" class="bg-red-500 text-white px-2 py-1 rounded shadow hover:bg-red-600 delete-btn" data-id="{{ $admin->id }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        // Configurar los botones de eliminación
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const adminId = this.getAttribute('data-id'); // Obtener el ID del administrador
                // Confirmar acción con SweetAlert2
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
                        // Realizar solicitud DELETE vía fetch
                        fetch(`/admin/admins/${adminId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            }
                        }).then(response => {
                            if (response.ok) {
                                // Eliminar fila de la tabla si es exitoso
                                document.getElementById(`admin-${adminId}`).remove();
                                Swal.fire(
                                    'Eliminado',
                                    'El administrador ha sido eliminado correctamente.',
                                    'success'
                                );
                            } else {
                                // Mostrar error si algo falla
                                Swal.fire(
                                    'Error',
                                    'Hubo un problema al eliminar el administrador.',
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
