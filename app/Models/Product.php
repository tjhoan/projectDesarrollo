<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'quantity',
        'brand',
        'category',
        'description'
    ];

    // Relación con imágenes
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Relación para el carrito
    public function carts()
    {
        return $this->belongsToMany(Cart::class)->withPivot('quantity');
    }
}
