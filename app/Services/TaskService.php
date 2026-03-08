<?php

namespace App\Services;

use App\Repo\TaskRepo;

class TaskService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected TaskRepo $taskRepo)
    {
        //
    }

    public function getAll()
    {
        return $this->taskRepo->getAll();
    }

    public function getPaginatedTasks(array $params)
    {
        return $this->taskRepo->getPaginated($params);
    }

    public function createTask(array $data)
    {
        return \DB::transaction(function () use ($data) {
            $taskData = [
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'creator_id' => auth()->id(),
            ];

            $task = $this->taskRepo->create($taskData);

            if (! empty($data['executor_ids'])) {
                $task->executors()->sync($data['executor_ids']);
            }

            return $task;
        });
    }

    public function view($id)
    {
        return $this->taskRepo->find($id);
    }

    public function updateTask(string $id, array $data)
    {
        $taskData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'creator_id' => auth()->id(),
        ];
        $task = $this->taskRepo->update($id, $taskData);
        if (! empty($data['executor_ids'])) {
            $task->executors()->sync($data['executor_ids']);
        }

        return $task;
    }
}
