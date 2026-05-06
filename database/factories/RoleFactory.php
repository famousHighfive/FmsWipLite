<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   // database/factories/RoleFactory.php

    public function definition(): array
    {
        return [
            // On pioche uniquement dans les valeurs autorisées par votre migration
            'name' => $this->faker->randomElement(['admin', 'cp', 'sup', 'tc']),
        ];
    }
}

