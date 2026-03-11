<?php

use App\Models\User;

it('get tasks', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->get(route('tasks.index'));
    $response->assertStatus(200);
});
it('create task', function () {
    $user = User::factory()->create();

    $data = [
        'title' => 'Test Task Title',
        'description' => 'Test task description content.',
        'due_date' => now()->addWeek()->format('Y-m-d'),
        'executor_ids' => [$user->id],
    ];

    $response = $this->actingAs($user)
        ->post(route('tasks.store'), $data);

    $response->assertRedirect(route('tasks.index'));
    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task Title',
    ]);
});
