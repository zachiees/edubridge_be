<?php
use App\Http\Controllers\Common\Lms;
use Illuminate\Support\Facades\Route;

Route::prefix('lms')->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('courses', [Lms::class, 'courses']);
});
