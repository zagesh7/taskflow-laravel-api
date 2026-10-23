<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/me',[AuthController::class,'me']);
    Route::get('/projects',[ProjectController::class,'index']);
    Route::get('/users',[UserController::class,'index']);
    Route::post('/projects',[ProjectController::class,'store'])->middleware('role:admin');
    Route::get('/projects/{project}',[ProjectController::class,'show']);
    Route::post('/projects/{project}/tasks',[TaskController::class,'store'])->middleware('role:admin');
    Route::patch('/tasks/{task}',[TaskController::class,'update']);
});
