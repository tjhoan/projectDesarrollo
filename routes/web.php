<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

// Ruta para la página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta para mostrar los detalles del producto desde el HomeController
Route::get('/products/{product}', [HomeController::class, 'details'])->name('products.details');

// Rutas para el carrito de compras
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Rutas de registro y autenticación de usuarios
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

// Agrupamos las rutas de administración bajo el middleware 'auth:admin' para que solo los administradores autenticados puedan acceder
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    // Ruta principal del admin panel (Dashboard)
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Rutas de administración para la gestión de productos, categorías y administradores
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('admins', AdminController::class);
});

// Rutas adicionales de productos con acciones específicas
Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/admin/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

require __DIR__ . '/auth.php';
