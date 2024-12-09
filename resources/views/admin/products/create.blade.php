@extends('admin.layouts.admin')

@section('content')
    <style>
        /* Estilo para el scroll del formulario */
        .form-container {
            max-height: 590px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #999 #f1f1f1;
        }

        .form-container::-webkit-scrollbar {
            width: 8px;
        }

        .form-container::-webkit-scrollbar-thumb {
            background-color: #999;
            border-radius: 10px;
        }

        .form-container::-webkit-scrollbar-track {
            background-color: #f1f1f1;
        }
    </style>

    <div class="main-content-wrapper bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold">Crear Nuevo Producto</h2>
        <div class="form-container">
            <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Nombre del producto -->
                <div class="mb-3">
                    <label class="block text-gray-700 font-bold mb-2" for="productName">Nombre</label>
                    <input name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" type="text" id="productName" placeholder="Enter product name" required>
                </div>

                <!-- Descripción del producto -->
                <div class="mb-1">
                    <label class="block text-gray-700 font-bold mb-2" for="description">Descripción</label>
                    <textarea name="description" class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" id="description" placeholder="Enter product description" required></textarea>
                </div>

                <!-- Precio -->
                <div class="mb-3">
                    <label class="block text-gray-700 font-bold mb-2" for="price">Precio</label>
                    <input name="price" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" type="number" id="price" placeholder="Enter price" required>
                </div>

                <!-- Categoría -->
                <div class="mb-3">
                    <label class="block text-gray-700 font-bold mb-2" for="category">Categoría</label>
                    <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" required>
                        <option value="">Selecciona una categoría</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Campo Público Objetivo -->
                <div class="mb-3">
                    <label class="block text-gray-700 font-bold mb-2" for="target_audience">Público Objetivo</label>
                    <select id="target_audience" name="target_audience" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" onchange="handleTargetAudienceChange(this)">
                        <option value="general">General</option>
                        <option value="mujer">Mujer</option>
                        <option value="hombre">Hombre</option>
                        <option value="niño">Niño</option>
                        <option value="niña">Niña</option>
                        <option value="unisex">Unisex</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <!-- Campo de entrada personalizado para 'Otro' -->
                <div id="custom_target_audience_wrapper" class="mb-3 hidden">
                    <label class="block text-gray-700 font-bold mb-2" for="custom_target_audience">Especificar Público Objetivo</label>
                    <input type="text" id="custom_target_audience" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" placeholder="Especificar público objetivo">
                </div>

                <!-- Campo de marca -->
                <div class="mb-3">
                    <label class="block text-gray-700 font-bold mb-2" for="brand">Marca</label>
                    <input name="brand" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" type="text" id="brand" placeholder="Ingresa la marca del producto" required>
                </div>

                <!-- Cantidad en stock -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="stockQuantity">Cantidad</label>
                    <input name="quantity" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" type="number" id="stockQuantity" placeholder="Enter stock quantity" required>
                </div>

                <!-- Botones para seleccionar entre URL o archivos -->
                <div class="mb-6 flex space-x-4">
                    <button type="button" id="url-btn" class="w-1/2 bg-blue-500 text-white py-2 rounded-md focus:outline-none">Usar URLs</button>
                    <button type="button" id="file-btn" class="w-1/2 bg-green-500 text-white py-2 rounded-md focus:outline-none">Subir Archivos</button>
                </div>

                <!-- Sección de subida de archivos -->
                <div id="file-section" class="mb-6 hidden">
                    <label class="block text-gray-700 font-bold mb-2" for="images">Subir Imágenes (máximo 5)</label>
                    <input type="file" name="images[]" id="images" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" multiple>
                </div>

                <!-- Sección de URLs de Imágenes -->
                <div id="url-section" class="mb-6 hidden">
                    <label class="block text-gray-700 font-bold mb-2">URLs de Imágenes</label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="image_urls[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" placeholder="URL de imagen 1">
                        <input type="text" name="image_urls[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" placeholder="URL de imagen 2">
                        <input type="text" name="image_urls[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" placeholder="URL de imagen 3">
                        <input type="text" name="image_urls[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" placeholder="URL de imagen 4">
                        <input type="text" name="image_urls[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-gray-200" placeholder="URL de imagen 5">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="w-full bg-gray-900 text-white py-2 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">Crear Producto</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('url-btn').addEventListener('click', function() {
            document.getElementById('file-section').classList.add('hidden');
            document.getElementById('url-section').classList.remove('hidden');
        });

        document.getElementById('file-btn').addEventListener('click', function() {
            document.getElementById('url-section').classList.add('hidden');
            document.getElementById('file-section').classList.remove('hidden');
        });

        function handleTargetAudienceChange(select) {
            const customAudienceWrapper = document.getElementById('custom_target_audience_wrapper');
            const customAudienceInput = document.getElementById('custom_target_audience');

            if (select.value === 'otro') {
                // Mostrar campo de entrada si selecciona "Otro"
                customAudienceWrapper.classList.remove('hidden');
                customAudienceInput.setAttribute('required', 'required');
            } else {
                // Ocultar campo de entrada y eliminar el valor de "Otro"
                customAudienceWrapper.classList.add('hidden');
                customAudienceInput.removeAttribute('required');
                customAudienceInput.value = '';
            }
        }

        document.getElementById('product-form').addEventListener('submit', function(event) {
            const targetAudienceSelect = document.getElementById('target_audience');
            const customAudienceInput = document.getElementById('custom_target_audience');

            if (targetAudienceSelect.value === 'otro' && customAudienceInput.value.trim()) {
                // Si se seleccionó 'Otro' y se ha llenado el campo personalizado, enviar el valor personalizado
                targetAudienceSelect.value = customAudienceInput.value;
            }
        });
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let errorMessages = "";
                @foreach ($errors->all() as $error)
                    errorMessages += "{{ $error }}\n";
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMessages,
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>
    @endif
@endsection
