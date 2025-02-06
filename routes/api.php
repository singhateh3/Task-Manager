<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Resources\Taskcollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Task resources
    Route::apiResource('/tasks', TaskController::class);
    // all users
    Route::get('/users', [UserController::class, 'index']);
    // logged in user to see all their tasks
    Route::get('/user/assigned/mytasks', [TaskController::class, 'myTasks']);
    // Route::get('/task/received/{task}', [TaskController::class, 'showMyTask']);
});

// Assign task to users based on userId and taskId
Route::post('/tasks/{taskId}/{userId}', [TaskController::class, 'assignTask']);
// a single task that has been assigned to users, taskid
Route::get('/tasks/assigned/{task}', [TaskController::class, 'showAssignedTask']);
// Fet all the users that have been assigned a task along with the tasks
Route::get('/tasks/all/assigned', [TaskController::class, 'allAssignedTasks']);

Route::get('/send-mail', [TestController::class, 'SendTestMail']);
Route::post('/{taskId}/file-upload', [TaskController::class, 'uploadFile']);
