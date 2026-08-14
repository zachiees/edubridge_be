<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\Evaluations;

Route::prefix('student/evaluations')
    ->middleware(['auth:sanctum','role_guard:student'])
    ->group(function () {
        Route::get('', [Evaluations::class, 'index']);
        Route::get('{uuid}', [Evaluations::class, 'find']);
        Route::post('{uuid}', [Evaluations::class, 'submit']);
    });
