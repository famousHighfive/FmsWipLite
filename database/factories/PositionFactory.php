<?php

// database/factories/PositionFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    public function definition(): array
    {
        // On définit les paires valides
        $positions = [
            ['name' => 'Ressource Humaine', 'code' => 'RH'],
            ['name' => 'Téléconseiller', 'code' => 'TC'],
            ['name' => 'Superviseur', 'code' => 'SUP'],
            ['name' => 'Chef Plateau', 'code' => 'CP'],
        ];

        $choice = $this->faker->randomElement($positions);

        return [
            'name' => $choice['name'],
            'code' => $choice['code'],
            'description' => $this->faker->sentence(10),
        ];
    }
}