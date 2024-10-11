<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Ruta para el dashboard (solo usuarios autenticados)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

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

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');


// Rutas para el carrito de compras (solo usuarios autenticados)
// Route::middleware(['auth'])->group(function () {
//     Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
//     Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
//     Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
// });

// Rutas para el pago (solo usuarios autenticados)
// Route::middleware(['auth'])->group(function () {
//     Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout.index');
//     Route::post('/checkout/pay', [PaymentController::class, 'process'])->name('checkout.process');
// });

require __DIR__ . '/auth.php';
