<?php

// database/factories/TimesheetEntryFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TimesheetEntryFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = '08:00:00';
        $checkOut = '17:00:00';
        $break = 60; // 1h de pause
        
        // Calcul simplifié pour la factory : (17-8) - 1 = 8h
        $total = 8.00;

        return [
            'date' => $this->faker->date(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'break_duration' => $break,
            'total_hours' => $total,
            'planned_hours' => 7.00,
            'overtime_hours' => 1.00, // 8h réalisé - 7h prévu
            'absence_type' => null,
            'comment' => $this->faker->optional(0.2)->sentence(),
        ];
    }
}