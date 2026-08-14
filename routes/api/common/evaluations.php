<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\Evaluations;

Route::prefix('common/evaluations')
    ->middleware(['auth:sanctum'])
    ->group(function () {

    Route::post('',[Evaluations::class, 'store'])->middleware(['role_guard:admin']);
    Route::get('', [Evaluations::class, 'index']);
    Route::delete('{uuid}', [Evaluations::class, 'destroy'])->middleware(['role_guard:admin']);
    Route::patch('{uuid}/set_visibility', [Evaluations::class, 'set_visibility'])->middleware(['role_guard:admin']);

    });
