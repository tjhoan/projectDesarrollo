<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        // Creamos algunos clientes
        Customer::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'address' => '123 Main St',
            'phone' => '1234567890',
            'id_number' => '123456',
            'gender' => 'male',
        ]);

        Customer::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
            'address' => '456 Elm St',
            'phone' => '0987654321',
            'id_number' => '654321',
            'gender' => 'female',
        ]);
    }
}
