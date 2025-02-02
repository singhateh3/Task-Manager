<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\Taskcollection;
use App\Http\Resources\TaskResouce;
use App\Http\Resources\TaskResourceCollection;
use App\Jobs\NewTaskJob;
use App\Models\Task;
use App\Models\User;
use App\Notifications\newTask;
use App\Notifications\NewTask as NotificationsNewTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

    public function assignTask(Request $request, $taskId, $userId)
    {
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

        return response()->json($taskUser);
    }

    public function allAssignedTasks()
    {
        // Fet all the users that have been assigned a task along with the tasks
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

    // public function showMyTask()
    // {
    //     $task = Task::con
    //     if (!$task->contains(auth()->user())) {
    //         return response()->json(['message' => 'This is not your task'], 404);
    //     }
    //     return response()->json($task);
    // }
}
