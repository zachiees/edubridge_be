<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\Courses;

Route::prefix('common/courses')
    ->middleware(['auth:sanctum'])
    ->group(function (){
        Route::post('', [Courses::class, 'store'])->middleware(['role_guard:admin']);
        Route::get('', [Courses::class, 'index']);
    });
