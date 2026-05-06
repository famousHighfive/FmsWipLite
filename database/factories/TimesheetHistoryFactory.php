<?php

// database/factories/TimesheetHistoryFactory.php
namespace Database\Factories;

use App\Models\Timesheet;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimesheetHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'timesheet_id' => Timesheet::inRandomOrder()->first() ?? Timesheet::factory(),
            'employee_id'  => Employee::inRandomOrder()->first() ?? Employee::factory(),
            'old_status'   => $this->faker->randomElement(['brouillon', 'soumis']),
            'new_status'   => $this->faker->randomElement(['soumis', 'valide']),
            'changed_by'   => User::inRandomOrder()->first()->id,
            'reason'       => $this->faker->sentence(),
            'created_at'   => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
