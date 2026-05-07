<?php

// database/seeders/AssignmentSeeder.php
namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Employee;
use App\Models\Campaign;
use App\Models\Position;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
 public function run(): void
{
    $campaigns = Campaign::all();
    // On récupère tous les superviseurs
    $superviseurs = Employee::whereHas('position', function($q) {
        $q->where('code', 'SUP');
    })->get();

    // On récupère tous les téléconseillers
    $teleconseillers = Employee::whereHas('position', function($q) {
        $q->where('code', 'TC');
    })->get();

    if ($superviseurs->isEmpty()) {
        $this->command->error("Il n'y a pas de Superviseurs pour gérer les TC !");
        return;
    }

    foreach ($teleconseillers as $tc) {
        Assignment::create([
            'employee_id' => $tc->id,
            'campaign_id' => $campaigns->random()->id,
            'manager_id'  => $superviseurs->random()->id, // Assigne un SUP aléatoire
            'position_id' => $tc->position_id,
            'status'      => 'actif',
            'start_date'  => now()->subMonths(2),
        ]);
    }
}
}