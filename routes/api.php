<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//COMMON

require_once 'api/common/auth.php';
require_once 'api/common/users.php';
require_once 'api/common/students.php';
