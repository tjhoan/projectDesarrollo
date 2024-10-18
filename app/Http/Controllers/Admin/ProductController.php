<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        // Cargar imágenes y subcategorías con eager loading
        $products = Product::with(['images', 'category', 'subcategory'])->get();

        return view('admin.products.index', compact('products'));
    }

    // Mostrar el formulario de creación de un nuevo producto
    public function create()
    {
        return view('admin.products.create');
    }

    // Mostrar el formulario de creación de productos
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'brand' => 'required|string',
            'category' => 'required|string',
            'description' => 'required|string',
            'images' => 'required',  // Validación para imágenes
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048' // Validación de tipos y tamaño
        ]);

        $product = Product::create($request->only(['name', 'price', 'quantity', 'brand', 'category', 'description']));

        // Guardado de imágenes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('public/images');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente');
    }

    // Mostrar el formulario de edición del producto
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('admin.products.edit', compact('product'));
    }

    // Actualizar el producto
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category' => 'required|string',
            'description' => 'required|string',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->only(['name', 'price', 'quantity', 'category', 'description']));

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Eliminar el producto
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente');
    }
}
