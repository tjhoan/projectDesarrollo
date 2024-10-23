<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'token' => $this->faker->uuid,
        ];
    }
}
