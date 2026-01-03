<?php
 
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
    
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
});
