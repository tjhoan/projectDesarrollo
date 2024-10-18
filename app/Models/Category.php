<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Relación con productos
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Relación con subcategorías
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
