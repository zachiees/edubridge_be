<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Courses extends Controller
{
    //
    public function index(Request $request){
        return $request->user()->student_courses;
    }
}
