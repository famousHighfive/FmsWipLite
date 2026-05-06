<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use App\Models\Position;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Récupérer ou créer les Rôles (slugs en minuscules comme dans ton RoleSeeder)
        $roleTc = Role::firstOrCreate(['name' => 'tc']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);

        // 2. Récupérer ou créer les Positions (VALEURS EXACTES DE L'ENUM)
        // Attention : 'Ressource Humaine' au lieu de 'Ressouce'
        $posRh = Position::firstOrCreate(
            ['code' => 'RH'], 
            ['name' => 'Ressource Humaine', 'description' => 'Département RH']
        );

        $posTc = Position::firstOrCreate(
            ['code' => 'TC'], 
            ['name' => 'Téléconseiller', 'description' => 'Agent de production']
        );

        // 3. Créer un Employé "Admin" spécifique
        $adminUser = User::factory()->create([
            'email' => 'admin@societe.com',
            'role_id' => $roleAdmin->id,
        ]);

        Employee::factory()->create([
            'user_id' => $adminUser->id,
            'first_name' => 'Jean',
            'last_name' => 'Admin',
            'email' => 'admin@societe.com',
            'position_id' => $posRh->id, // Utilise la position RH valide
            'status' => 'actif',
        ]);

        // 4. Créer 10 employés aléatoires avec le rôle 'tc' et la position 'TC'
        Employee::factory(10)->create([
            'position_id' => $posTc->id, // Utilise la position Téléconseiller valide
            'user_id' => User::factory()->state(['role_id' => $roleTc->id]),
        ]);
    }
}