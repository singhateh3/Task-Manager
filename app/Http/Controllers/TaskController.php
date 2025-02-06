<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\AssignTaskResource;
use App\Http\Resources\TaskResouce;
use App\Http\Resources\TaskResourceCollection;
use App\Models\Task;
use App\Models\User;
use App\Notifications\NewTask as NotificationsNewTask;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Gate;
// use Illuminate\Auth\Access\Response;

class TaskController extends Controller
{
    public function index()
    {
        $this->authorize('admin-action');
        $tasks = Task::all();
        return response()->json(['message' => 'All tasks', 'Tasks' => new TaskResourceCollection($tasks)]);
    }

    public function store(TaskRequest $request)
    {
        $this->authorize('admin-and-manager-action');
        $validated = $request->validated();


        $task = Task::create($validated);
        return response()->json([
            'message' => 'Task created successfully',
            'Task' => new TaskResouce($task)
        ]);
    }

    public function update(TaskRequest $request, $id)
    {
        $task = Task::find($id);
        $validated = $request->validated();
        $task->update($validated);
        return response()->json([
            'message' => 'Task updated successfully',
            'Task' => new TaskResouce($task)
        ]);
    }
    public function show($id)
    {
        $task = Task::find($id);
        return response()->json([
            'message' => 'Task updated successfully',
            'Task' => new TaskResouce($task)
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('admin-and-manager-action');
        $task = Task::find($id);
        $task->delete();
        return response()->json([
            'message' => 'Task deleted successfully',
        ], 201);
    }

    public function assignTask($taskId, $userId)
    {
        $this->authorize('admin-and-manager-action');

        $user = User::find($userId);
        $task = Task::find($taskId);

        if (!$user || !$task) {
            return response()->json(['error' => 'User or Task not found'], 404);
        }

        // Attach user to the task
        $task->users()->attach($user->id);

        // Load the task with assigned users
        // $taskUser = $task->load('users');
        $taskUser = $task->users()->orderByPivot('created_at', 'desc')->first();

        // Send notification
        $user->notify(new NotificationsNewTask($task));

        return response()->json(new AssignTaskResource($taskUser));
    }

    public function allAssignedTasks()
    {
        // Fetch all the users that have been assigned a task along with the tasks
        $assignedTasks = Task::withWhereHas('users')->get();

        // Return the tasks and their users
        return response()->json([
            'message' => 'A list of all assigned tasks',
            'tasks' => $assignedTasks
        ], 200);
    }

    public function showAssignedTask($taskId)
    {
        $task = Task::find($taskId);
        $taskUser = $task->load('users');

        return response()->json(
            [
                'message' => 'Here is the list of all the users and their assigned tasks',
                'tasks' => $taskUser
            ],
            200
        );
    }

    public function myTasks()
    {
        // logged in user to see all their tasks
        $tasks = auth()->user()->tasks;
        return response()->json(
            [
                'message' => 'Here is the list of all the tasks assigned to you',
                'tasks' => $tasks
            ],
            200
        );
    }

    public function uploadFile(Request $request, $id)
    {
        // fetch the task and upload a file to it
        $task = Task::find($id);

        $request->validate([
            'file' => ['required', 'file']
        ]);

        $path = $request->file('file')->store('tasks', 'public');

        $task->update(['file' => $path]);
        return response()->json(['message' => 'file upload successful!', 'file_path' => Storage::url($path), 'task' => new TaskResouce($task)], 201);
    }
}
