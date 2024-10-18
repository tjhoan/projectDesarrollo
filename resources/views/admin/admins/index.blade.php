@extends('admin.layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Gestión de Administradores</h2>
        <a href="{{ route('admins.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow">Crear Administrador</a>
    </div>

    <!-- Tabla de Administradores -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-3 px-4 border-b">Nombre</th>
                    <th class="py-3 px-4 border-b">Correo Electrónico</th>
                    <th class="py-3 px-4 border-b">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                    <tr>
                        <td class="py-3 px-4 border-b">{{ $admin->name }}</td>
                        <td class="py-3 px-4 border-b">{{ $admin->email }}</td>
                        <td class="py-3 px-4 border-b">
                            <a href="{{ route('admins.edit', $admin->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded shadow hover:bg-yellow-600">Editar</a>
                            <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" class="inline-block">
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
