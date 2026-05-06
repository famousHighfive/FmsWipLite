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
        // 1. Récupérer les rôles
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleCp = Role::firstOrCreate(['name' => 'cp']);
        $roleSup = Role::firstOrCreate(['name' => 'sup']);
        $roleTc = Role::firstOrCreate(['name' => 'tc']);

        // 2. Récupérer les positions
        $posRh = Position::firstOrCreate(
            ['code' => 'RH'],
            ['name' => 'Ressource Humaine', 'description' => 'Département RH']
        );

        $posCp = Position::firstOrCreate(
            ['code' => 'CP'],
            ['name' => 'Chef Plateau', 'description' => 'Chef de plateau']
        );

        $posSup = Position::firstOrCreate(
            ['code' => 'SUP'],
            ['name' => 'Superviseur', 'description' => 'Superviseur']
        );

        $posTc = Position::firstOrCreate(
            ['code' => 'TC'],
            ['name' => 'Téléconseiller', 'description' => 'Agent de production']
        );

        // 3. Créer 2 admins
        for ($i = 1; $i <= 2; $i++) {
            $adminUser = User::factory()->create([
                'email' => "admin{$i}@societe.com",
                'role_id' => $roleAdmin->id,
            ]);

            Employee::factory()->create([
                'user_id' => $adminUser->id,
                'first_name' => $i === 1 ? 'Jean' : 'Marie',
                'last_name' => $i === 1 ? 'Admin' : 'Admin',
                'email' => "admin{$i}@societe.com",
                'position_id' => $posRh->id,
                'status' => 'actif',
            ]);
        }

        // 4. Créer 10 chefs plateau
        Employee::factory(10)->create([
            'position_id' => $posCp->id,
            'user_id' => User::factory()->state(['role_id' => $roleCp->id]),
        ]);

        // 5. Créer 20 superviseurs
        Employee::factory(20)->create([
            'position_id' => $posSup->id,
            'user_id' => User::factory()->state(['role_id' => $roleSup->id]),
        ]);

        // 6. Créer 268 téléconseillers (300 total - 2 - 10 -20 = 268)
        Employee::factory(268)->create([
            'position_id' => $posTc->id,
            'user_id' => User::factory()->state(['role_id' => $roleTc->id]),
        ]);
    }
}
