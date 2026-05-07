<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        // 1. On crée les rôles fixes
        $roles = collect(['admin', 'cp', 'sup', 'tc'])->map(function ($name) {
            return Role::create(['name' => $name]);
        });

        // 2. Créer un Admin spécifique pour se connecter
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role_id' => $roles->where('name', 'admin')->first()->id,
            'password' => Hash::make('1234'),
        ]);

        // 3. Créer 20 utilisateurs aléatoires répartis sur les rôles existants
        User::factory(300)->create([
            'role_id' => fn() => $roles->random()->id,
            'password' => Hash::make('1234'),
        ]);
    }
}
