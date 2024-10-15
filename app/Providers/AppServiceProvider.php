<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
    {
        View::composer(['layouts.app', 'cart_items', 'cart'], function ($view) {
            $cart = null;
            if (Auth::check()) {
                $cart = Cart::where('customer_id', Auth::id())->first();
            } else {
                $token = request()->cookie('cart_token');
                if ($token) {
                    $cart = Cart::where('token', $token)->first();
                }
            }

            $view->with('cart', $cart);
        });
    }
}
