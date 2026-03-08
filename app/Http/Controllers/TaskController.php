<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Services\TaskLogService;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;
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
        $tasks = $this->taskService->getPaginatedTasks($filters);

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

        return redirect()->route('tasks.index');
    }

    public function show(string $id)
    {
        $task = $this->taskService->view($id);

        return Inertia::render('admin/tasks/view', [
            'task' => $task,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $filters = $request->only(['search', 'limit', 'page', 'sort_by', 'sort_order']);
        $users = $this->userService->getPaginatedUsers($filters);
        $task = $this->taskService->view($id);

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

        return redirect()->route('tasks.show', $id);
    }
}
