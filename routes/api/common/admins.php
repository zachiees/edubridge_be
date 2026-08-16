<?php
use App\Http\Controllers\Common\Admins;
use Illuminate\Support\Facades\Route;

Route::prefix('common/admins')
        ->middleware(['auth:sanctum'])
        ->group(function () {
            Route::post('', [Admins::class, 'store'])->middleware(['role_guard:admin']);
            Route::get('',  [Admins::class, 'index']);
            Route::delete('{uuid}', [Admins::class, 'destroy'])->middleware(['role_guard:admin']);

        });
