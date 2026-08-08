<?php
use App\Http\Controllers\Common\Lms;
use Illuminate\Support\Facades\Route;

Route::prefix('common/lms')->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('courses',               [Lms::class, 'courses']);
        Route::get('courses_categories',    [Lms::class, 'courses_categories']);
        Route::get('roles',                 [Lms::class, 'roles']);
        Route::get('users_by_role',         [Lms::class, 'users_by_role']);
});
