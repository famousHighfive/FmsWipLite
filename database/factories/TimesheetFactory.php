<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimesheetFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', 'now');
        $start->modify('monday this week');

        $end = (clone $start);
        $end->modify('sunday this week');

        return [
            'employee_id' => Employee::factory(),

            'period_start' => $start->format('Y-m-d'),
            'period_end'   => $end->format('Y-m-d'),

            'status' => $this->faker->randomElement([
                'brouillon',
                'soumis',
                'valide'
            ]),

            'validated_by' => null,
            'validated_at' => null,
        ];
    }
}
