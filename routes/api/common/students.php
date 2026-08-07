<?php
use App\Http\Controllers\Common\Students;
use Illuminate\Support\Facades\Route;

Route::prefix('common/students')
        ->middleware(['auth:sanctum'])
        ->group(function () {
            Route::post('', [Students::class, 'store'])->middleware(['role_guard:admin']);
            Route::get('',  [Students::class, 'index']);
            Route::delete('{uuid}', [Students::class, 'destroy'])->middleware(['role_guard:admin']);

        });
