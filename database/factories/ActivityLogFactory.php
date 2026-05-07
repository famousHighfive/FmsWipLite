<?php

// database/factories/ActivityLogFactory.php
namespace Database\Factories;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::inRandomOrder()->first()->id,
            'action'      => $this->faker->randomElement(['login', 'create', 'update', 'delete', 'export']),
            'model_type'  => 'App\Models\Employee',
            'model_id'    => Employee::inRandomOrder()->first()->id,
            'description' => $this->faker->sentence(),
            'ip_address'  => $this->faker->ipv4(),
            'created_at'  => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
