<?php

// database/factories/CampaignFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        // On ajoute entre 1 et 6 mois à la date de début pour la date de fin
        $endDate = (clone $startDate)->modify('+' . rand(1, 6) . ' months');

        return [
            'name' => $this->faker->company() . ' - ' . $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['active', 'inactive', 'terminee']),
        ];
    }
}
