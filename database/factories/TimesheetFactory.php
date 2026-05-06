<?php

// database/factories/TimesheetFactory.php
namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimesheetFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->startOfWeek();
        return [
            'employee_id' => Employee::inRandomOrder()->first() ?? Employee::factory(),
            'period_start' => $start,
            'period_end' => (clone $start)->endOfWeek(),
            'status' => $this->faker->randomElement(['brouillon', 'soumis', 'valide']),
            'validated_by' => Employee::whereHas('position', fn($q) => $q->where('code', 'CP'))->inRandomOrder()->first()?->id,
            'validated_at' => now(),
        ];
    }
}
