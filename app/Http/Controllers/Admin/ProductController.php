<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Cargar imágenes y categorías con eager loading
        $products = Product::with(['images', 'category'])->get();

        return view('admin.products.index', compact('products'));
    }

    // Mostrar el formulario de creación de un nuevo producto
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }


    // Mostrar el formulario de creación de productos
    public function store(Request $request)
    {
        // Validación de los datos
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:products,name',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'brand' => 'required|string|max:255',
                'category_id' => 'required|integer',
                'target_audience' => 'required|string',
                'description' => 'required|string',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:8192',
                'image_urls' => 'nullable|array|max:5',
                'image_urls.*' => 'nullable|url',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // Verificar si la cantidad total de imágenes (subidas y URLs) no excede el límite
        $totalImages = 0;

        if ($request->hasFile('images')) {
            $totalImages += count($request->file('images'));
        }

        if ($request->has('image_urls')) {
            $totalImages += count(array_filter($request->input('image_urls')));
        }

        if ($totalImages > 6) {
            return redirect()->back()->withErrors(['message' => 'Solo puedes subir un máximo de 5 imágenes.'])->withInput();
        }

        // Crear el producto
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'brand' => $request->brand,
            'category_id' => $request->category_id,
            'target_audience' => $request->target_audience,
            'description' => $request->description,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
            }
        }

        // Guardado de imágenes subidas
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $mimeType = $image->getClientMimeType();

                // Almacenar la imagen en el directorio 'public/images' y obtener la ruta pública
                $path = $image->store('images', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path
                ]);
            }
        }

        // Guardado de URLs de imágenes
        if ($request->input('image_urls')) {
            foreach ($request->input('image_urls') as $url) {
                if (!empty($url)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $url
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Producto eliminado correctamente']);
    }
}
