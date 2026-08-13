<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\Courses;

Route::prefix('student/courses')
    ->middleware(['auth:sanctum','role_guard:student'])
    ->group(function () {
        Route::get('', [Courses::class, 'index']);
    });
