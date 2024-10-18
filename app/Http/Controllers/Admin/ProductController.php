<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        // Cargar las categorías con sus subcategorías
        $categories = Category::with('subcategories')->get();

        // Pasar las categorías a la vista
        return view('admin.products.create', compact('categories'));
    }

    // Mostrar el formulario de creación de productos
    public function store(Request $request)
    {
        // Log para verificar los datos que llegan desde el formulario
        Log::info('Datos recibidos para crear el producto:', $request->all());

        // dd('Llega hasta aquí antes de la validación');

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'brand' => 'required|string',
                'category_id' => 'required|integer',
                'description' => 'required|string',
                'images' => 'nullable',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'image_urls' => 'nullable|array',
                'image_urls.*' => 'nullable|url',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', $e->errors());
            return back()->withErrors($e->errors());
        }

        Log::info('Datos de categoría seleccionada:', ['category_id' => $request->category_id]);

        // Crear el producto
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'brand' => $request->brand,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

        // Log para verificar si el producto se creó correctamente
        Log::info('Producto creado:', ['product_id' => $product->id ?? 'Producto no creado']);

        // Guardado de imágenes subidas
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            $totalImages = count($images) + count($request->input('image_urls', []));

            if ($totalImages > 5) {
                Log::error('Se excedió el número de imágenes permitidas.');
                return redirect()->back()->withErrors(['message' => 'Solo puedes subir un máximo de 5 imágenes.']);
            }

            foreach ($images as $image) {
                // Almacenar la imagen en el directorio 'public/images' y obtener la ruta pública
                $path = $image->store('images', 'public'); // Almacenar en public/images
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path
                ]);

                // Log para indicar que las imágenes fueron guardadas
                if ($request->hasFile('images')) {
                    dd($request->file('images'));
                }
            }

            // Log para indicar que las imágenes fueron guardadas
            Log::info('Imágenes subidas correctamente para el producto', ['product_id' => $product->id]);
        }

        // Guardado de URLs de imágenes
        Log::info('Guardando URLs de imágenes:', $request->input('image_urls'));

        // Guardado de URLs de imágenes
        if ($request->input('image_urls')) {
            foreach ($request->input('image_urls') as $url) {
                if ($url !== null) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $url
                    ]);
                }
            }

            // Log para indicar que las URLs fueron guardadas
            Log::info('URLs de imágenes guardadas correctamente para el producto', ['product_id' => $product->id]);
        }

        Log::info('Producto creado:', ['product_id' => $product->id ?? 'Producto no creado']);

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente');
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
