<?php

namespace App\Services;

use App\Repo\TaskRepo;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $task = $this->taskRepo->find($id)->lockForUpdate();

        $taskData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
        ];

        $task = $this->taskRepo->update($id, $taskData);

        if (! empty($data['executor_ids'])) {
            $task->executors()->sync($data['executor_ids']);
        }

        return $task;
    }

    public function updateTaskStatus($id, $status)
    {
        $task = $this->taskRepo->find($id)->lockForUpdate();

        if (! $task) {
            throw new NotFoundHttpException('Task not found');
        }

        $task->executors()->updateExistingPivot(Auth::id(), ['status' => $status]);

        return $task;
    }
}
