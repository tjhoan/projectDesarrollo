@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-10 px-4">
        <h1 class="text-4xl font-bold mb-10 text-center text-gray-800">Finalizar Compra</h1>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form id="checkout-form" action="{{ route('payment.process') }}" method="POST">
                @csrf

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
                        <option value="nequi">Nequi</option>
                        <option value="daviplata">Daviplata</option>
                        <option value="bancolombia">Bancolombia a la Mano</option>
                    </select>
                </div>

                <div class="flex items-center mb-4">
                    <input type="checkbox" name="pdf_invoice" id="pdf_invoice" class="mr-2">
                    <label for="pdf_invoice" class="text-gray-700">Generar Factura PDF</label>
                </div>

                <div class="flex items-center mb-4">
                    <input type="checkbox" name="email_invoice" id="email_invoice" class="mr-2">
                    <label for="email_invoice" class="text-gray-700">Enviar Factura por Correo</label>
                </div>

                <button type="button" id="confirm-payment" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">
                    Completar Compra
                </button>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div id="payment-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Completar el Pago</h2>
            <p id="payment-details"></p>
            <div class="mt-4">
                <button id="confirm-payment" class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600">
                    Confirmar Pago
                </button>
                <button id="close-modal" class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de éxito -->
    <div id="success-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">¡Pago Completado!</h2>
            <p id="modal-message"></p>
            <p>Número de factura: <span id="invoice-number"></span></p>
            <div class="mt-4">
                <button id="close-modal" class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('confirm-payment').addEventListener('click', function() {
            let paymentMethod = document.getElementById('payment_method').value;
            let paymentDetails = '';

            // Dependiendo del método de pago, mostramos diferentes detalles
            switch (paymentMethod) {
                case 'nequi':
                    paymentDetails = 'Escanea este código QR para completar tu pago con Nequi.';
                    break;
                case 'daviplata':
                    paymentDetails = 'Escanea este código QR para completar tu pago con Daviplata.';
                    break;
                case 'bancolombia':
                    paymentDetails = 'Usa el número de cuenta 1234 5678 9012 para transferir a Bancolombia.';
                    break;
                default:
                    paymentDetails = 'Selecciona un método de pago válido.';
            }

            // Mostrar los detalles de pago en el modal
            document.getElementById('payment-details').textContent = paymentDetails;

            // Mostrar el modal
            document.getElementById('payment-modal').classList.remove('hidden');
        });

        document.getElementById('close-modal').addEventListener('click', function() {
            // Ocultar el modal
            document.getElementById('payment-modal').classList.add('hidden');
        });

        document.getElementById('confirm-payment').addEventListener('click', function() {
            // Enviar el formulario después de confirmar el pago
            document.getElementById('checkout-form').submit();
        });
    </script>

    <script>
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
                        document.getElementById('success-modal').classList.remove('hidden');
                    } else {
                        alert('Hubo un problema al procesar el pago');
                    }
                })
                .catch(error => {
                    console.error('Error al procesar el pago:', error);
                    alert('Hubo un error inesperado.');
                });

        });

        document.getElementById('close-modal').addEventListener('click', function() {
            // Ocultar el modal
            document.getElementById('success-modal').classList.add('hidden');
            window.location.href = "{{ route('home') }}"; // Redirigir a la tienda o alguna otra página
        });
    </script>
@endsection
