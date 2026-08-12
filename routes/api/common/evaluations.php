<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\Evaluations;

Route::prefix('common/evaluations')->group(function () {
    Route::post('',[Evaluations::class, 'store'])->middleware(['role_guard:admin']);
});
