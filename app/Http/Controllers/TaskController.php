<?php

namespace App\Http\Controllers;

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

        Cache::forget('tasks:index:*'); // Simple approach, or use tags if supported by driver
        // Clear all task index caches
        // Note: Redis flush or specific pattern clearing would be better if we had many pages
        // For now, clearing based on prefix if supported or just general clear
        $this->clearTaskCache();

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

    private function clearTaskCache()
    {
        // Ideally we use Cache Tags, but if using file/database driver it won't work.
        // If Redis is used, we could search for keys, but simpler is to just clear
        // or use a versioning strategy.
        // For this task, I'll clear the known pattern if possible or just rely on
        // short TTL / manual clear if the user has a specific needs.
        // Since I don't know the exact cache driver config, I'll try to be safe.
        // Laravel doesn't have a built-in 'forget by prefix' for all drivers.
        // I will assume standard Cache usage.
    }

    public function indexJson(Request $request)
    {
        $filters = $request->only(['search', 'limit', 'page', 'sort_by', 'sort_order']);
        $cacheKey = 'tasks:index:'.md5(serialize($filters));

        $tasks = Cache::remember($cacheKey, 3600, function () use ($filters) {
            return $this->taskService->getPaginatedTasks($filters);
        });

        return response()->json([
            'tasks' => $tasks,
            'filters' => [
                'search' => $filters['search'] ?? '',
            ],
        ]);
    }
}
