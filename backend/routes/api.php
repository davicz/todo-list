<?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\TaskController;
    use App\Http\Controllers\Api\UserController;

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']); // Vamos adicionar o logout também

        Route::get('/tasks/export', [TaskController::class, 'export']);
        Route::post('/tasks', [TaskController::class, 'store'])->middleware('role:admin');
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::get('/tasks/{task}', [TaskController::class, 'show']);
        Route::put('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    
        Route::prefix('admin')->middleware('role:admin')->group(function () {
            Route::apiResource('users', UserController::class)->only(['index', 'update', 'destroy']);
    });
    
    });