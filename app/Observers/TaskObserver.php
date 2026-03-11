<?php

namespace App\Observers;

use App\Jobs\TaskAssignedMailJob;
use App\Models\Task;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class TaskObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        // We load the executors to ensure they are available
        $task->load('executors');

        foreach ($task->executors as $user) {
            dispatch(new TaskAssignedMailJob($user->email, $task));

        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
