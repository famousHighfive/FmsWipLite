<?php

// database/factories/AssignmentHistoryFactory.php
namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Employee;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'assignment_id'   => Assignment::inRandomOrder()->first() ?? Assignment::factory(),
            'employee_id'     => Employee::inRandomOrder()->first() ?? Employee::factory(),
            'old_manager_id'  => User::inRandomOrder()->first()->id,
            'new_manager_id'  => User::inRandomOrder()->first()->id,
            'old_campaign_id' => Campaign::inRandomOrder()->first()->id,
            'new_campaign_id' => Campaign::inRandomOrder()->first()->id,
            'action_type'     => $this->faker->randomElement(['assign', 'release', 'transfer']),
            'changed_by'      => User::inRandomOrder()->first()->id,
            'reason'          => $this->faker->sentence(),
            'created_at'      => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}