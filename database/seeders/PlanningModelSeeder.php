<?php

// database/seeders/PlanningModelSeeder.php
namespace Database\Seeders;

use App\Models\PlanningModel;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlanningModelSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::first()->id ?? 1;

        // Modèle classique 35h
        PlanningModel::create([
            'name' => 'Standard 35h',
            'description' => '7h par jour du lundi au vendredi',
            'monday_hours' => 7, 'tuesday_hours' => 7, 'wednesday_hours' => 7,
            'thursday_hours' => 7, 'friday_hours' => 7,
            'saturday_hours' => 0, 'sunday_hours' => 0,
            'total_hours' => 35,
            'created_by' => $adminId,
        ]);

        // Modèle Shift Samedi 40h
        PlanningModel::create([
            'name' => 'Production intensive 40h',
            'description' => '8h par jour incluant le samedi',
            'monday_hours' => 8, 'tuesday_hours' => 8, 'wednesday_hours' => 0,
            'thursday_hours' => 8, 'friday_hours' => 8,
            'saturday_hours' => 8, 'sunday_hours' => 0,
            'total_hours' => 40,
            'created_by' => $adminId,
        ]);
        
        // Optionnel : quelques modèles aléatoires
        PlanningModel::factory(3)->create(['created_by' => $adminId]);
    }
}