<?php

namespace App\Services;

use App\Repo\TaskLogRepo;
use Illuminate\Support\Facades\Auth;

class TaskLogService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private TaskLogRepo $taskLogRepo)
    {
        //
    }

    public function createLog($type, $taskId, $status = null)
    {
        switch ($type) {
            case 'create':
                $message = 'Task has been created.';
                break;
            case 'update':
                $message = 'Task has been updated.';
                break;
            case 'status_update':
                $message = "Task status has been updated to {$status}.";
                break;
            default:
                $message = 'Task action performed.';
                break;
        }
        $data = [
            'task_id' => $taskId,
            'user_id' => Auth::id(),
            'action' => $type,
            'description' => $message,
        ];

        $this->taskLogRepo->create($data);
    }
}
