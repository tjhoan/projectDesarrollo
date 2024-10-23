<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'quantity' => $this->faker->numberBetween(1, 50),
            'brand' => $this->faker->company,
            'category_id' => Category::factory(),
            'description' => $this->faker->sentence,
            'target_audience' => $this->faker->randomElement(['Adultos', 'Niños', 'Unisex']),
        ];
    }
}
