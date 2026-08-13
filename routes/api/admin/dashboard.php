<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Dashboard;

Route::prefix('admin/dashboard')
    ->middleware(['auth:sanctum','role_guard:admin'])
    ->group(function () {
        Route::get('/stats', [Dashboard::class, 'stats']);
    });
