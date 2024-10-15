<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

// Ruta para la página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta para ver los detalles de un producto
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Rutas para la sección de administración (solo para usuarios autenticados y administradores)
Route::prefix('admin')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
});

// Rutas para el carrito de compras
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

require __DIR__ . '/auth.php';
