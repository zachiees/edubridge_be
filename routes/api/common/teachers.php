<?php
use App\Http\Controllers\Common\Teachers;
use Illuminate\Support\Facades\Route;

Route::prefix('common/teachers')
        ->middleware(['auth:sanctum'])
        ->group(function () {
            Route::post('', [Teachers::class, 'store'])->middleware(['role_guard:admin']);
            Route::get('',  [Teachers::class, 'index']);

        });
