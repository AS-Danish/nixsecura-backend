<?php
 
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\GalleryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Image upload endpoint (protected)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/upload-image', [\App\Http\Controllers\ImageUploadController::class, 'upload']);
    Route::delete('/delete-image', [\App\Http\Controllers\ImageUploadController::class, 'delete']);
});

// Public routes
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::get('/workshops', [WorkshopController::class, 'index']);
Route::get('/workshops/{id}', [WorkshopController::class, 'show']);
Route::get('/testimonials', [TestimonialController::class, 'index']);
Route::get('/testimonials/{id}', [TestimonialController::class, 'show']);
Route::get('/faculty', [FacultyController::class, 'index']);
Route::get('/faculty/{id}', [FacultyController::class, 'show']);
Route::get('/certificates', [CertificateController::class, 'index']);
Route::get('/certificates/{id}', [CertificateController::class, 'show']);
Route::get('/gallery', [GalleryController::class, 'index']);
Route::get('/gallery/{id}', [GalleryController::class, 'show']);

// Protected admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Blogs
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
    
    // Courses
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
    
    // Workshops
    Route::post('/workshops', [WorkshopController::class, 'store']);
    Route::put('/workshops/{id}', [WorkshopController::class, 'update']);
    Route::delete('/workshops/{id}', [WorkshopController::class, 'destroy']);
    
    // Testimonials
    Route::post('/testimonials', [TestimonialController::class, 'store']);
    Route::put('/testimonials/{id}', [TestimonialController::class, 'update']);
    Route::delete('/testimonials/{id}', [TestimonialController::class, 'destroy']);
    
    // Faculty
    Route::post('/faculty', [FacultyController::class, 'store']);
    Route::put('/faculty/{id}', [FacultyController::class, 'update']);
    Route::delete('/faculty/{id}', [FacultyController::class, 'destroy']);
    
    // Certificates
    Route::post('/certificates', [CertificateController::class, 'store']);
    Route::put('/certificates/{id}', [CertificateController::class, 'update']);
    Route::delete('/certificates/{id}', [CertificateController::class, 'destroy']);
    
    // Gallery
    Route::post('/gallery', [GalleryController::class, 'store']);
    Route::put('/gallery/{id}', [GalleryController::class, 'update']);
    Route::delete('/gallery/{id}', [GalleryController::class, 'destroy']);
});
