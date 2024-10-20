@extends('admin.layouts.admin')

@section('content')
    <style>
        /* Estilos similares a los productos */
        .hover:bg-gray-100 {
            transition: background-color 0.3s ease;
        }
    </style>

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold mt-4" style="margin-left: 50px">Gestión de Administradores</h2>
        <a href="{{ route('admins.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow mr-4 mt-4">Crear Administrador</a>
    </div>

    <!-- Tabla de Administradores -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-3 px-4 border-b text-left">Nombre</th>
                    <th class="py-3 px-4 border-b text-left">Correo Electrónico</th>
                    <th class="py-3 px-4 border-b text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                    <tr id="admin-{{ $admin->id }}" class="hover:bg-gray-100">
                        <td class="py-3 px-4 border-b">{{ $admin->name }}</td>
                        <td class="py-3 px-4 border-b">{{ $admin->email }}</td>
                        <td class="py-3 px-4 border-b">
                            <button type="button" class="bg-red-500 text-white px-2 py-1 rounded shadow hover:bg-red-600 delete-btn" data-id="{{ $admin->id }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        // Función para eliminar un administrador usando AJAX y SweetAlert
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const adminId = this.getAttribute('data-id');
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
                        fetch(`/admin/admins/${adminId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            }
                        }).then(response => {
                            if (response.ok) {
                                // Eliminamos la fila del administrador y mostramos un mensaje de éxito con SweetAlert
                                document.getElementById(`admin-${adminId}`).remove();
                                Swal.fire(
                                    'Eliminado',
                                    'El administrador ha sido eliminado correctamente.',
                                    'success'
                                );
                            } else {
                                // Mensaje de error si algo sale mal
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
