@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-10 px-4">
        <h1 class="text-4xl font-bold mb-10 text-center text-gray-800">Tu Carrito de Compras</h1>
        <div class="bg-white rounded-lg shadow-lg p-6">
            @include('cart_items', ['cart' => $cart])
        </div>
    </div>
@endsection
