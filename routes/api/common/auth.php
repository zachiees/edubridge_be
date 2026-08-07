<?php

use App\Http\Controllers\Common\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('common/auth')->group(function () {
    Route::post('login',    [Auth::class, 'login']);
    Route::post('logout',   [Auth::class, 'login']);
    Route::get('validate', [Auth::class, 'validate'])->middleware(['auth:sanctum']);
});
