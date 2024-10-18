@extends('admin.layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6">Edit Product</h2>
        <form action="{{ route('products.update', $product->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-gray-700">Product Name</label>
                <input type="text" name="name" id="name" value="{{ $product->name }}" class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300" required>
            </div>

            <div>
                <label for="description" class="block text-gray-700">Description</label>
                <textarea name="description" id="description" class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300" required>{{ $product->description }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-gray-700">Price</label>
                    <input type="number" name="price" id="price" value="{{ $product->price }}" class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300" required>
                </div>

                <div>
                    <label for="quantity" class="block text-gray-700">Stock Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ $product->quantity }}" class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300" required>
                </div>
            </div>

            <div>
                <label for="category" class="block text-gray-700">Category</label>
                <input type="text" name="category" id="category" value="{{ $product->category }}" class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300" required>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg shadow hover:bg-blue-600">Update Product</button>
        </form>
    </div>
@endsection
