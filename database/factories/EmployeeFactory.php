<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
// Dans database/factories/EmployeeFactory.php
public function definition(): array
{
    return [
        'user_id' => User::factory(),
        'matricule' => $this->faker->unique()->bothify('EMP-####'),
        'first_name' => $this->faker->firstName(),
        'last_name' => $this->faker->lastName(),
        'birth_date' => $this->faker->date('Y-m-d', '-18 years'),
        'phone' => $this->faker->phoneNumber(),
        'email' => $this->faker->unique()->safeEmail(),
        'position_id' => Position::inRandomOrder()->first()->id ?? Position::factory(),
        'salary_base' => $this->faker->randomFloat(2, 1500, 4500),
        'status' => 'actif',
    ];
}
}
