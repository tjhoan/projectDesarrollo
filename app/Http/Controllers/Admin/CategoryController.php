<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('subcategories')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',  // Validación de la descripción
            'subcategories' => 'array',
            'subcategories.*' => 'string|min:3|max:255',
        ]);

        // Crear la categoría con la descripción
        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,  // Guardar la descripción
        ]);

        // Crear las subcategorías si existen
        if ($request->has('subcategories')) {
            foreach ($request->subcategories as $subcategoryName) {
                Subcategory::create([
                    'name' => $subcategoryName,
                    'category_id' => $category->id,
                ]);
            }
        }

        return redirect()->route('categories.index')->with('success', 'Categoría creada con éxito');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'subcategories' => 'array',
            'subcategories.*' => 'string|min:3|max:255',
        ]);

        // Actualizar la categoría
        $category->update(['name' => $request->name]);

        // Actualizar las subcategorías
        $category->subcategories()->delete(); // Borra las subcategorías actuales

        foreach ($request->subcategories as $subcategoryName) {
            Subcategory::create([
                'name' => $subcategoryName,
                'category_id' => $category->id, 
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada con éxito');
    }

    public function destroy(Category $category)
    {
        // Eliminar la categoría y sus subcategorías relacionadas
        $category->delete();

        // Retornar una respuesta exitosa en formato JSON
        return response()->json(['success' => true]);
    }
}
