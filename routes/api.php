<?php

use App\Http\Controllers\Api\users\userController;
use App\Http\Controllers\tasks\taskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Users Apis
 */
Route::post('/v1/usersList',[userController::class,'list']);

/**
 * Tasks Apis
 */
Route::post('/v1/create-task',[taskController::class,'createTask']);
Route::post('/v1/list-task',[taskController::class,'listTask']);
Route::post('/v1/update-status-task',[taskController::class,'updateTask']);
Route::post('/v1/delete-task',[taskController::class,'deleteTask']);
Route::post('/v1/counter-task',[taskController::class,'counterTask']);
