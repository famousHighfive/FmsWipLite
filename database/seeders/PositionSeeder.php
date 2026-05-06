<?php

// database/seeders/PositionSeeder.php
namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Ressource Humaine',
                'code' => 'RH',
                'description' => 'Gestion du personnel et recrutement.'
            ],
            [
                'name' => 'Téléconseiller',
                'code' => 'TC',
                'description' => 'Agent en charge des appels entrants/sortants.'
            ],
            [
                'name' => 'Superviseur',
                'code' => 'SUP',
                'description' => 'Responsable de l\'encadrement d\'une équipe de TC.'
            ],
            [
                'name' => 'Chef Plateau',
                'code' => 'CP',
                'description' => 'Pilote de la production et garant des objectifs.'
            ],
        ];

        foreach ($data as $position) {
            Position::firstOrCreate(['code' => $position['code']], $position);
        }
    }
}
