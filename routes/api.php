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
require_once 'api/common/teachers.php';
require_once 'api/common/admins.php';
require_once 'api/common/courses.php';
require_once 'api/common/lms.php';
require_once 'api/common/evaluations.php';

//ADMIN

require_once 'api/admin/dashboard.php';

//STUDENT

require_once 'api/student/courses.php';
require_once 'api/student/evaluation.php';
