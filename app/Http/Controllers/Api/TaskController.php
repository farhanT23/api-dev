<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    public function index()
    {
        return response()->json([
            'tasks' => Auth::user()->tasks,
        ], 200);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        $this->authorizeUser($task);

        return response()->json($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorizeUser($task);

        $task->update($request->validated());

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $this->authorizeUser($task);

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }

    protected function authorizeUser(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
