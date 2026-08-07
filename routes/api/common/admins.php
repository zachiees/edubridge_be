<?php
use App\Http\Controllers\Common\Admins;
use Illuminate\Support\Facades\Route;

Route::prefix('common/admins')
        ->middleware(['auth:sanctum'])
        ->group(function () {
            Route::get('',  [Admins::class, 'index']);

        });
