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

    public function createLog($type, $taskId)
    {
        switch ($type) {
            case 'create':
                $message = 'Task has been created.';
                break;
            case 'update':
                $message = 'Task status has been updated.';
                break;
            default:
                $message = 'Task status has been deleted.';
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
