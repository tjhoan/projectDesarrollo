<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
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
        $request->validate([
            'name' => 'required|string|min:3|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        foreach ($request->subcategories as $sub) {
            Subcategory::create([
                'name' => $sub,
                'category_id' => $category->id,
            ]);
        }

        return response()->json(['message' => 'Categoría creada con éxito'], 201);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
        ]);

        $category->update(['name' => $request->name]);
        return response()->json(['message' => 'Categoría actualizada con éxito']);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Categoría eliminada con éxito']);
    }
}
