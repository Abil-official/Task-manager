<?php

namespace App\Http\Controllers;

use App\Exports\ExportTask;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Services\TaskLogService;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private UserService $userService,
        private TaskLogService $taskLogService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'limit', 'page', 'sort_by', 'sort_order']);
        $cacheKey = 'tasks:index:'.md5(serialize($filters));

        $tasks = Cache::remember($cacheKey, 3600, function () use ($filters) {
            return $this->taskService->getPaginatedTasks($filters);
        });

        return Inertia::render('admin/tasks/index', [
            'tasks' => $tasks,
            'filters' => [
                'search' => $filters['search'] ?? '',
            ],
        ]);
    }

    public function create(Request $request)
    {
        $filters = $request->only(['search', 'limit', 'page', 'sort_by', 'sort_order']);
        $users = $this->userService->getPaginatedUsers($filters);

        return Inertia::render('admin/tasks/create', [
            'users' => $users,
        ]);
    }

    public function store(CreateTaskRequest $request)
    {
        $data = $request->validated();

        $task = $this->taskService->createTask($data);
        $this->taskLogService->createLog(type: 'create', taskId: $task->id);

        Cache::forget('tasks:index:*');

        return redirect()->route('tasks.index');
    }

    public function show(string $id)
    {
        $task = Cache::remember('task:show:'.$id, 3600, function () use ($id) {
            return $this->taskService->view($id);
        });

        return Inertia::render('admin/tasks/view', [
            'task' => $task,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $filters = $request->only(['search', 'limit', 'page', 'sort_by', 'sort_order']);

        $users = $this->userService->getPaginatedUsers($filters);
        $task = Cache::remember('task:edit:'.$id, 3600, function () use ($id) {
            return $this->taskService->view($id);
        });

        return Inertia::render('admin/tasks/edit', [
            'task' => $task,
            'users' => $users,
        ]);
    }

    public function update(CreateTaskRequest $request, $id)
    {
        $data = $request->validated();
        $task = $this->taskService->updateTask($id, $data);
        $this->taskLogService->createLog(type: 'update', taskId: $task->id);

        Cache::forget('task:show:'.$id);

        return redirect()->route('tasks.show', $id);
    }

    public function updateTaskStatus(UpdateTaskStatusRequest $request)
    {
        $validated = $request->validated();

        $task = $this->taskService->updateTaskStatus($validated['id'], $validated['status']);
        $this->taskLogService->createLog(type: 'status_update', taskId: $task->id, status: $validated['status']);

        Cache::forget('task:show:'.$validated['id']);

        return redirect()->back();
    }

    public function exportTask()
    {

        return (new ExportTask)->download('tasks.xlsx');
    }
}
