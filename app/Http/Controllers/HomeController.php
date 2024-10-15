<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener todas las categorías
        $categories = Category::all();

        // Obtener todos los productos
        $products = Product::with('images')->get();

        // Retornar la vista 'home' con las categorías y productos
        return view('home', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
