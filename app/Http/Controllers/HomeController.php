<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Obtener todas las categorías
        $categories = Category::all();

        // Verificar si se está filtrando por categoría
        $query = Product::with('images');

        if ($request->has('category_id') && $request->get('category_id') !== null) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Obtener los productos paginados (15 por página)
        $products = $query->paginate(15);

        // Si es una solicitud AJAX (por ejemplo, al hacer scroll), devolver solo los productos como HTML
        if ($request->ajax()) {
            $html = view('partials.product_list', compact('products'))->render();
            return response()->json(['html' => $html]);
        }

        // Retornar la vista 'home' con las categorías y productos
        return view('home', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
