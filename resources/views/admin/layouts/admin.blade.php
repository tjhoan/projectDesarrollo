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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <style>
        #toggleSidebar {
            position: fixed;
            top: 5rem;
            left: 13rem;
            z-index: 50;
            background-color: #1f2937;
            color: white;
            padding: 0.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease-in-out;
        }

        #content {
            transition: margin-left 0.3s ease-in-out;
        }

        @media (max-width: 1024px) {
            #toggleSidebar {
                top: 5rem;
                left: 1rem;
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <!-- Navbar -->
    @include('layouts.navigation')

    <!-- Toggle Sidebar Button -->
    <button id="toggleSidebar" class="shadow-md">
        <span class="material-icons">menu</span>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gray-100 shadow-lg transform -translate-x-full transition-transform duration-300">
        <nav class="mt-32">
            <ul>
                <li class="mb-6">
                    <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-900 hover:bg-gray-300 rounded-md">
                        <span class="material-icons mr-2">category</span>
                        Productos
                    </a>
                </li>
                <li class="mb-6">
                    <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-900 hover:bg-gray-300 rounded-md">
                        <span class="material-icons mr-2">view_list</span>
                        Categorías
                    </a>
                </li>
                <li class="mb-6">
                    <a href="{{ route('admins.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-900 hover:bg-gray-300 rounded-md">
                        <span class="material-icons mr-2">admin_panel_settings</span>
                        Administradores
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div id="content" class="p-7 mt-4 transition-all duration-300">
        @yield('content')
    </div>

    <script>
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');

        // Inicializa el estado del sidebar
        const initializeSidebar = () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.add('translate-x-0');
                sidebar.classList.remove('-translate-x-full');
                content.style.marginLeft = '16rem';
                toggleSidebar.style.left = '13rem';
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                content.style.marginLeft = '0';
                toggleSidebar.style.left = '1rem';
            }
        };

        // Alternar estado del sidebar
        toggleSidebar.addEventListener('click', () => {
            const isHidden = sidebar.classList.contains('-translate-x-full');

            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                if (window.innerWidth >= 1024) {
                    content.style.marginLeft = '16rem';
                    toggleSidebar.style.left = '13rem';
                } else {
                    content.style.marginLeft = '10rem';
                    toggleSidebar.style.left = '13rem';
                }
            } else {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                content.style.marginLeft = '0';
                toggleSidebar.style.left = '1rem';
            }
        });

        // Ajustar sidebar al redimensionar la ventana
        window.addEventListener('resize', initializeSidebar);

        // Inicializa el estado al cargar la página
        initializeSidebar();
    </script>
</body>

</html>
