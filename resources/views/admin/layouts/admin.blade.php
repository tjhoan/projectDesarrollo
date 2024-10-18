<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs/build/css/alertify.min.css" />
    <script src="//cdn.jsdelivr.net/npm/alertifyjs/build/alertify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Transición para el sidebar */
        .sidebar-transition {
            transition: all 0.3s ease;
        }

        /* Ocultar completamente el sidebar */
        .hidden-sidebar {
            display: none;
        }

        /* Ajuste para el contenido principal */
        .main-content-full {
            width: 100%;
            margin-left: 0;
        }

        #sidebar {
            height: 91vh;
        }

        /* Botón flotante para mostrar/ocultar sidebar */
        #toggleSidebar {
            top: 1rem;
        }

        /* Alinear al centro el formulario */
        .main-content-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 10px;
        }

        /* Añadir espacio superior */
        .main-content-wrapper {
            margin-top: 25px;
        }

        @media (max-width: 768px) {
            #toggleSidebar {
                left: 1rem;
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    @include('layouts.navigation')

    <!-- Botón para mostrar/ocultar sidebar -->
    <button id="toggleSidebar" class="fixed mt-16 left-52 z-50 bg-gray-800 text-white p-2 rounded-md focus:outline-none transition-all duration-300">
        <span class="material-icons">menu</span>
    </button>

    <!-- Contenedor principal -->
    <div id="mainContainer" class="flex">
        <!-- Sidebar -->
        <div id="sidebar" class="w-48 bg-gray-200 sidebar-transition h-screen relative">
            <div class="p-6">
                <h1 id="sidebarTitle" class="text-3xl font-bold text-gray-900 transition-all duration-300">ACME<span id="sidebarText" class="inline"> Inc.</span></h1>
            </div>
            <nav class="mt-10">
                <ul id="sidebarItems">
                    <li class="mb-6">
                        <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-900 hover:bg-gray-300 rounded-md">
                            <span class="material-icons mr-2">category</span>
                            <span>Productos</span>
                        </a>
                    </li>
                    <li class="mb-6">
                        <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-900 hover:bg-gray-300 rounded-md">
                            <span class="material-icons mr-2">view_list</span>
                            <span>Categorías</span>
                        </a>
                    </li>
                    <li class="mb-6">
                        <a href="{{ route('admins.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-900 hover:bg-gray-300 rounded-md">
                            <span class="material-icons mr-2">admin_panel_settings</span>
                            <span>Administradores</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- Parte inferior del sidebar para perfil -->
            <div id="profileSection" class="absolute bottom-0 left-0 w-full p-4 bg-gray-300 transition-all duration-300">
                <p class="text-sm text-gray-500">John Doe<br />Admin</p>
            </div>
        </div>

        <!-- Main content -->
        <div id="content" class="flex-1 px-6 content">
            @yield('content')
        </div>
    </div>

    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <!-- Script para mostrar/ocultar sidebar -->
    <script>
        const toggleSidebarButton = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('content');

        toggleSidebarButton.addEventListener('click', () => {
            // Alternar la visibilidad del sidebar
            sidebar.classList.toggle('hidden-sidebar');
            mainContent.classList.toggle('main-content-full');

            // Mover el botón cuando el sidebar está visible o escondido
            if (sidebar.classList.contains('hidden-sidebar')) {
                toggleSidebarButton.style.left = '1rem'; // Cuando está cerrado
            } else {
                toggleSidebarButton.style.left = '13rem'; // Cuando está abierto
            }
        });
    </script>
</body>

</html>
