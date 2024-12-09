<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Detalles del producto
Route::get('/products/{product}', [HomeController::class, 'details'])->name('products.details');

// Carrito de compras
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Registro y autenticación de usuarios
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

// Rutas de administración bajo middleware 'auth:admin'
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    // Dashboard admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Gestión de productos, categorías y administradores
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('admins', AdminController::class);
});

// Rutas adicionales de productos (CRUD)
Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
Route::delete('/admin/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

// Descargar PDF de la factura
Route::get('/invoice/download/{invoiceNumber}', function ($invoiceNumber) {
    $filePath = "public/invoices/{$invoiceNumber}.pdf";

    if (Storage::exists($filePath)) {
        return Storage::download($filePath);
    } else {
        return abort(404, 'Archivo no encontrado.');
    }
})->name('invoice.download');

// Página de checkout
Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

// Procesar el pago y generar factura
Route::middleware('auth')->group(function () {
    Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.process');
});

// Página de confirmación de pago completado
Route::get('/payment/complete', function () {
    return view('payment_complete');
})->name('payment.complete');

require __DIR__ . '/auth.php';
