@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-10 px-4">
        <h1 class="text-4xl font-bold mb-10 text-center text-gray-800">Finalizar Compra</h1>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form id="checkout-form" action="{{ route('payment.process') }}" method="POST">
                @csrf
                <!-- Campos del formulario -->
                <div class="mb-4">
                    <label for="full_name" class="block text-gray-700">Nombre Completo</label>
                    <input type="text" name="full_name" id="full_name" class="w-full border border-gray-300 p-2 rounded" required>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="address" class="block text-gray-700">Dirección de Envío</label>
                        <input type="text" name="address" id="address" class="w-full border border-gray-300 p-2 rounded" required>
                    </div>
                    <div>
                        <label for="state" class="block text-gray-700">Barrio</label>
                        <input type="text" name="state" id="state" class="w-full border border-gray-300 p-2 rounded" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="city" class="block text-gray-700">Ciudad</label>
                        <input type="text" name="city" id="city" class="w-full border border-gray-300 p-2 rounded" required>
                    </div>
                    <div>
                        <label for="phone" class="block text-gray-700">Teléfono</label>
                        <input type="text" name="phone" id="phone" class="w-full border border-gray-300 p-2 rounded" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="additional_info" class="block text-gray-700">Información Adicional</label>
                    <textarea name="additional_info" id="additional_info" class="w-full border border-gray-300 p-2 rounded"></textarea>
                </div>

                <div class="mb-4">
                    <label for="payment_method" class="block text-gray-700">Método de Pago</label>
                    <select name="payment_method" id="payment_method" class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="daviplata" selected>Daviplata</option>
                        <option value="bancolombia">Bancolombia a la Mano</option>
                    </select>
                </div>

                <div class="flex items-center mb-4">
                    <input type="checkbox" name="pdf_invoice" id="pdf_invoice" class="mr-2" checked>
                    <label for="pdf_invoice" class="text-gray-700">Generar Factura PDF</label>
                </div>

                <!-- Al tocar este botón se abre el modal payment-modal -->
                <button type="button" id="complete-purchase" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">
                    Completar Compra
                </button>
            </form>
        </div>
    </div>

    <!-- Modal de confirmación de pago -->
    <div id="payment-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg relative">
            <button id="close-payment-modal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                &times;
            </button>
            <h2 class="text-xl font-bold mb-4">Completar el Pago</h2>
            <p id="payment-details"></p>
            <div id="payment-image-container" class="my-4 text-center">
                <!-- Aquí se cargará la imagen del QR dependiendo del método de pago -->
                <img id="payment-qr-image" src="" alt="QR de método de pago" class="mx-auto" style="max-width: 200px; display: none;">
            </div>
            <div class="mt-4">
                <!-- Este botón ahora confirma el pago -->
                <button id="confirm-payment" class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600">
                    Confirmar Pago
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de éxito -->
    <div id="success-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg relative" style="width: 500px;">
            <button id="close-success-modal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                &times;
            </button>
            <h2 class="text-xl font-bold mb-4 text-center">¡Pago Completado!</h2>
            <p id="modal-message"></p>
            <p class="text-center">Número de factura: <span id="invoice-number"></span></p>
            <div class="mt-4 flex justify-between">
                <a id="download-invoice" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600" href="#">
                    Descargar Factura PDF
                </a>
                <a href="{{ route('home') }}" class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600 ml-4">
                    Ir al Home
                </a>
            </div>
        </div>
    </div>

    <script>
        // Función para cargar la imagen QR según el método de pago
        function updateQRImage() {
            let selectedPaymentMethod = document.getElementById('payment_method').value;
            let paymentImage = document.getElementById('payment-qr-image');

            if (selectedPaymentMethod === 'daviplata') {
                paymentImage.src = "{{ asset('img/QR/QRDaviPlata.jpg') }}";
                paymentImage.style.display = 'block';
            } else if (selectedPaymentMethod === 'bancolombia') {
                paymentImage.src = "{{ asset('img/QR/QRBancolombiaALaMano.jpg') }}";
                paymentImage.style.display = 'block';
            } else {
                paymentImage.style.display = 'none';
            }
        }

        // Mostrar el modal de confirmación al tocar "Completar Compra"
        document.getElementById('complete-purchase').addEventListener('click', function() {
            document.getElementById('payment-modal').classList.remove('hidden');
            updateQRImage(); // Cargar la imagen QR al abrir el modal
        });

        // Cambiar la imagen según el método de pago seleccionado
        document.getElementById('payment_method').addEventListener('change', updateQRImage);

        // Confirmar pago y mostrar modal de éxito
        document.getElementById('confirm-payment').addEventListener('click', function() {
            let form = document.getElementById('checkout-form');
            let formData = new FormData(form);

            fetch('{{ route('payment.process') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modal-message').textContent = data.message;
                        document.getElementById('invoice-number').textContent = data.invoice_number;
                        document.getElementById('download-invoice').href = `/invoice/download/${data.invoice_number}`;
                        // Ocultar el modal de pago y mostrar el modal de éxito
                        document.getElementById('payment-modal').classList.add('hidden');
                        document.getElementById('success-modal').classList.remove('hidden');
                    } else {
                        // Mostrar SweetAlert cuando hay un error en el procesamiento del pago
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al procesar el pago',
                            text: 'Hubo un problema al procesar el pago, por favor inténtalo de nuevo.',
                        });
                    }
                })
                .catch(error => {
                    // Mostrar SweetAlert cuando hay un error inesperado
                    console.error('Error al procesar el pago:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error inesperado',
                        text: 'Ocurrió un error inesperado al procesar el pago. Por favor, inténtalo más tarde.',
                    });
                });
        });

        // Cerrar modales con la X
        document.getElementById('close-payment-modal').addEventListener('click', function() {
            document.getElementById('payment-modal').classList.add('hidden');
        });

        document.getElementById('close-success-modal').addEventListener('click', function() {
            document.getElementById('success-modal').classList.add('hidden');
        });

        // Ejecutar la función updateQRImage cuando la página cargue para asegurar que el QR por defecto se cargue
        window.addEventListener('DOMContentLoaded', updateQRImage);
    </script>
@endsection
