@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-10 px-4">
        <h1 class="text-4xl font-bold mb-10 text-center text-gray-800">Tu Carrito de Compras</h1>
        <div class="bg-white rounded-lg shadow-lg p-6">
            @include('cart_items', ['cart' => $cart])
        </div>
    </div>

    <script>
        function removeCartItem(itemId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas eliminar este producto del carrito?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('cart.remove', '') }}/" + itemId,
                        method: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            $('#cart-item-' + itemId).remove();
                            $('.cart-counter').text(response.cartItemCount);
                            if (response.cartItemCount === 0) {
                                location.reload();
                            }
                            Swal.fire(
                                'Eliminado',
                                'El producto ha sido eliminado del carrito.',
                                'success'
                            );
                        },
                        error: function(error) {
                            console.error('Error al eliminar el producto:', error);
                            Swal.fire(
                                'Error',
                                'Hubo un problema al eliminar el producto del carrito.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function clearCart() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esto eliminará todos los productos del carrito.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, vaciar carrito',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('cart.clear') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },
                        success: function() {
                            location.reload();
                            Swal.fire(
                                'Carrito Vaciado',
                                'Todos los productos fueron eliminados del carrito.',
                                'success'
                            );
                        },
                        error: function(error) {
                            console.error('Error al vaciar el carrito:', error);
                            Swal.fire(
                                'Error',
                                'Hubo un problema al vaciar el carrito.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endsection
