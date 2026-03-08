<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService,
        private UserService $userService
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'executor_ids' => 'nullable|array',
            'executor_ids.*' => 'exists:users,id',
        ]);

        $this->taskService->createTask($data);

        return redirect()->route('tasks.index');
    }
}
