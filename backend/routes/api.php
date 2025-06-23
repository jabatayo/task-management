<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ContactController;

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

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1'); // 5 requests per minute
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // 5 requests per minute

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Task routes
    Route::apiResource('tasks', TaskController::class);
    
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Contact routes
    Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:10,1'); // 10 requests per minute
});

// Public static routes
Route::get('/about', [ContactController::class, 'about']); 