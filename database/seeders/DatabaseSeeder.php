<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskExecutor;
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
        User::factory(50)->create()->each(function ($u) {
            $u->userInfo()->create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
            ]);
        });
        $users = User::all();

        Task::factory(20)->create()->each(function ($task) use ($users) {
            $executors = $users->random(rand(2, 5));
            foreach ($executors as $executor) {
                $task->executors()->attach($executor->id, [
                    'status' => fake()->randomElement(['pending', 'in_progress', 'completed']),
                ]);
            }
        });
    }
}
