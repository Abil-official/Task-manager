<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::firstOrCreate(
            ['email' => 'dev@mailinator.com'],
            ['password' => bcrypt('password')]
        );

        $user->userInfo()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => 'Abil',
                'last_name' => 'Rijal',
            ]
        );

        // Seed some extra users for testing
        User::factory(10)->create()->each(function ($u) {
            $u->userInfo()->create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
            ]);
        });
    }
}
