<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'full_name' => $this->faker->name,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'phone' => $this->faker->phoneNumber,
            'payment_method' => 'daviplata',
            'pdf_invoice' => true,
            'confirmation_code' => $this->faker->randomNumber(6),
        ];
    }
}
