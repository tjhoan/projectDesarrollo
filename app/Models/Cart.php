<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'token',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_items')->withPivot('quantity');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // Método para calcular el total del carrito
    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->product->price * $item->quantity;
        }
        return $total;
    }

    // Eliminar items al eliminar carrito
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($cart) {
            $cart->items()->delete();
        });
    }
}
