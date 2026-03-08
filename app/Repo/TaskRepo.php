<?php

namespace App\Repo;

use App\Models\Task;

class TaskRepo implements BaseRepo
{
    // -- initialize  Task
    public function __construct(private Task $task)
    {
        //
    }

    public function getAll()
    {
        return $this->task->all();
    }

    public function find(string $id)
    {
        return $this->task->with([
            'creator.userInfo',
            'executors.userInfo',
            'taskLogs.user.userInfo'
        ])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->task->create($data);
    }

    public function update(string $id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);

        return $record;
    }

    public function delete(string $id)
    {
        return $this->find($id)->delete();
    }

    public function getPaginated(array $params)
    {
        $query = $this->task->query()->with(['creator.userInfo', 'executors.userInfo']);

        $query = $this->applySearch($query, $params['search'] ?? null);
        $query = $this->applySort($query, $params['sort_by'] ?? 'created_at', $params['sort_order'] ?? 'desc');

        return $query->paginate($params['limit'] ?? 10, ['*'], 'page', $params['page'] ?? 1);
    }

    private function applySearch($query, $search)
    {
        return $query->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        });
    }

    private function applySort($query, $sortBy, $sortOrder)
    {
        return $query->orderBy($sortBy, $sortOrder);
    }
}
