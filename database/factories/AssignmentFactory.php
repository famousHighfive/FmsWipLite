<?php

// database/factories/AssignmentFactory.php
namespace Database\Factories;

use App\Models\Employee;
use App\Models\Campaign;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        
        return [
            // On pioche un employé existant (obligatoire selon ta migration)
            'employee_id' => Employee::inRandomOrder()->first() ?? Employee::factory(),
            
            // On pioche une campagne existante
            'campaign_id' => Campaign::inRandomOrder()->first() ?? Campaign::factory(),
            
            // Le manager est un employé existant (optionnel/nullable)
            'manager_id' => Employee::inRandomOrder()->first()?->id,
            
            // On pioche une position existante (RH, TC, SUP ou CP)
            'position_id' => Position::inRandomOrder()->first() ?? Position::factory(),
            
            'status' => $this->faker->randomElement(['actif', 'terminé', 'suspendu']),
            'start_date' => $startDate,
            'end_date' => $this->faker->optional(0.5)->dateTimeBetween($startDate, '+6 months'),
        ];
    }
}