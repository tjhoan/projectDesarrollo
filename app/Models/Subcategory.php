<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id'];

    // Relación con la tabla categories
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación con la tabla products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
