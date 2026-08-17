<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\Evaluations;

Route::prefix('common/evaluations')
    ->middleware(['auth:sanctum','role_guard:admin'])
    ->group(function () {

    Route::post('',[Evaluations::class, 'store']);
    Route::get('', [Evaluations::class, 'index']);;
    Route::get('{uuid}', [Evaluations::class, 'find']);
    Route::delete('{uuid}',               [Evaluations::class, 'destroy']);
    Route::get('{uuid}/respondents',      [Evaluations::class, 'respondents']);
    Route::patch('{uuid}/set_visibility', [Evaluations::class, 'set_visibility']);
    Route::get('{uuid}/student_progress', [Evaluations::class, 'student_progress']);
    Route::get('{uuid}/summary', [Evaluations::class, 'summary']);
    Route::get('{uuid}/teacher_summary', [Evaluations::class, 'teacher_summary']);
});
