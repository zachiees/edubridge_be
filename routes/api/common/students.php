<?php
use App\Http\Controllers\Common\Students;
use Illuminate\Support\Facades\Route;

Route::prefix('common/students')
        ->middleware(['auth:sanctum'])
        ->group(function () {
            Route::post('', [Students::class, 'store']);
            Route::get('',  [Students::class, 'index']);

        });
