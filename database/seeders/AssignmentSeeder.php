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
        // On récupère nos données de base
        $campaigns = Campaign::all();
        $positions = Position::all();
        $employees = Employee::all();

        if ($employees->isEmpty() || $campaigns->isEmpty()) {
            $this->command->error("D'abord, remplissez les tables employees et campaigns !");
            return;
        }

        // On identifie un manager potentiel (ex: un Chef Plateau ou Superviseur)
        $manager = Employee::whereHas('position', function($q) {
            $q->whereIn('code', ['CP', 'SUP']);
        })->first() ?? $employees->first();

        // 1. On assigne manuellement quelques employés pour avoir des données propres
        foreach ($employees->take(10) as $emp) {
            Assignment::create([
                'employee_id' => $emp->id,
                'campaign_id' => $campaigns->random()->id,
                'manager_id'  => $manager->id,
                'position_id' => $emp->position_id, // On garde sa position d'origine
                'status'      => 'actif',
                'start_date'  => now()->subMonths(2),
                'end_date'    => null,
            ]);
        }

        // 2. On génère 15 autres affectations aléatoires (historique/terminé)
        Assignment::factory(15)->create([
            'status' => 'terminé'
        ]);
    }
}