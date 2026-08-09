<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\Courses;

Route::prefix('common/courses')
    ->middleware(['auth:sanctum'])
    ->group(function (){
        Route::post('',       [Courses::class, 'store'])->middleware(['role_guard:admin']);
        Route::get('',        [Courses::class, 'index']);
        Route::post('import', [Courses::class, 'import'])->middleware(['role_guard:admin']);
        Route::get('list',    [Courses::class, 'list']);
        Route::get('{uuid}',           [Courses::class, 'find'])->middleware(['role_guard:admin']);
        Route::patch('{uuid}',         [Courses::class, 'update'])->middleware(['role_guard:admin']);
        Route::delete('{uuid}',        [Courses::class, 'destroy'])->middleware(['role_guard:admin']);
        Route::post('{uuid}/students', [Courses::class, 'add_students'])->middleware(['role_guard:admin']);
    });
