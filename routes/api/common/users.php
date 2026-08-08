<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\Users;

Route::prefix('common/users')
            ->middleware(['auth:sanctum'])
            ->group(function () {
                Route::post('/import',  [Users::class, 'import'])->middleware('role_guard:admin');
            });
