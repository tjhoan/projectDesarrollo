<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Categoría creada con éxito');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
        ]);

        $category->update(['name' => $request->name]);

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada con éxito');
    }

    public function destroy(Category $category)
    {
        // Eliminar la categoría relacionada
        $category->delete();

        // Retornar una respuesta exitosa en formato JSON
        return response()->json(['success' => true]);
    }
}
