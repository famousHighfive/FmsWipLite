<?php

// database/factories/PlanningModelFactory.php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanningModelFactory extends Factory
{
    public function definition(): array
    {
        // On génère des heures types (7h, 8h ou 0h)
        $h = [
            'mon' => $this->faker->randomElement([7, 8]),
            'tue' => $this->faker->randomElement([7, 8]),
            'wed' => $this->faker->randomElement([7, 8]),
            'thu' => $this->faker->randomElement([7, 8]),
            'fri' => $this->faker->randomElement([7, 8]),
            'sat' => 0,
            'sun' => 0,
        ];

        return [
            'name' => $this->faker->word() . ' Shift',
            'description' => $this->faker->sentence(),
            'monday_hours'    => $h['mon'],
            'tuesday_hours'   => $h['tue'],
            'wednesday_hours' => $h['wed'],
            'thursday_hours'  => $h['thu'],
            'friday_hours'    => $h['fri'],
            'saturday_hours'  => $h['sat'],
            'sunday_hours'    => $h['sun'],
            'total_hours'     => array_sum($h),
            'created_by'      => User::inRandomOrder()->first() ?? User::factory(),
        ];
    }
}