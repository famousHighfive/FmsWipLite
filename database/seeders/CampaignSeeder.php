<?php

// database/seeders/CampaignSeeder.php
namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création de campagnes spécifiques
        Campaign::create([
            'name' => 'Support Technique Orange',
            'description' => 'Campagne de gestion des incidents ADSL/Fibre.',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'status' => 'active',
        ]);

        Campaign::create([
            'name' => 'Prospection Assurance',
            'description' => 'Appels sortants pour vente de contrats mutuelle.',
            'start_date' => now()->subMonths(2),
            'end_date' => now()->addMonths(4),
            'status' => 'active',
        ]);

        // 2. Générer 5 campagnes aléatoires supplémentaires
        Campaign::factory(5)->create();
    }
}