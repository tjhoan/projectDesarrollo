<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

// Ruta para la página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta para ver los detalles de un producto
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Ruta principal del admin panel (Dashboard)
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth:admin'])->name('admin.dashboard');

// Rutas para la sección de administración (solo para usuarios autenticados y administradores)
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    // Productos
    Route::resource('products', ProductController::class);

    // Categorías y subcategorías
    Route::resource('categories', CategoryController::class);
    Route::resource('subcategories', SubcategoryController::class);

    // Administradores
    Route::resource('admins', AdminController::class);
});

Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('products.update');

// Rutas para el carrito de compras
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Rutas para el registro y autenticación
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

require __DIR__ . '/auth.php';
