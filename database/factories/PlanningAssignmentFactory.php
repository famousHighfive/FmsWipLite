<?php

// database/factories/PlanningAssignmentFactory.php
namespace Database\Factories;

use App\Models\Employee;
use App\Models\PlanningModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanningAssignmentFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', 'now');
        $status = $this->faker->randomElement(['en attente', 'validé', 'suspendu']);

        return [
            'planning_model_id' => PlanningModel::inRandomOrder()->first() ?? PlanningModel::factory(),
            'employee_id'       => Employee::inRandomOrder()->first() ?? Employee::factory(),
            'start_date'        => $startDate,
            'end_date'          => (clone $startDate)->modify('+3 months'),
            'status'            => $status,
            'validated_by'      => $status === 'validé' 
                                    ? Employee::whereHas('position', fn($q) => $q->whereIn('code', ['CP', 'SUP']))->inRandomOrder()->first()?->id 
                                    : null,
            'validated_at'      => $status === 'validé' ? now() : null,
        ];
    }
}