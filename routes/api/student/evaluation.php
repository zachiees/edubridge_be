<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\Evaluation;

Route::prefix('student/evaluation')
    ->middleware(['auth:sanctum','role_guard:student'])
    ->group(function () {
        Route::get('', [Evaluation::class, 'index']);
    });
