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
    'category_id',
    'description',
    'target_audience'
  ];

  // Relación con imágenes
  public function images()
  {
    return $this->hasMany(ProductImage::class);
  }

  // Relación con la categoría
  public function category()
  {
    return $this->belongsTo(Category::class);
  }
}
