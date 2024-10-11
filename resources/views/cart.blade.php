@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-10 px-4">
        <h1 class="text-4xl font-bold mb-10 text-center text-gray-800">Tu Carrito de Compras</h1>

        @if ($cart->items->isEmpty())
            <p class="text-center text-gray-600">Tu carrito está vacío.</p>
        @else
            <div class="bg-white rounded-lg shadow-lg p-6">
                @foreach ($cart->items as $item)
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <img src="{{ asset($item->product->images->first()->image_path) }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover mr-4">
                            <div>
                                <h5 class="font-bold text-lg">{{ $item->product->name }}</h5>
                                <p class="text-gray-600">Cantidad: {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <div>
                            <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="mt-6 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition">Vaciar Carrito</button>
                </form>
            </div>
        @endif
    </div>
@endsection
