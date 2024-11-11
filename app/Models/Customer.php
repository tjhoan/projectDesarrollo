<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function cart()
    {
        return $this->hasOne(Cart::class)->withDefault();
    }

    // Eliminar carrito al eliminar cliente
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($customer) {
            $customer->cart()->delete();
        });
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
