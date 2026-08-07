<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;

class Courses extends Controller
{
    //
    public function store(Request $request){
        $request->validate([
            'name'       => 'required|string|max:200',
            'code'       => 'required|string|max:50',
            'teacher_id' => ['nullable','exists:users,uuid'],
            'lms_id'     => 'nullable|integer|unique:App\Models\Course,lms_id',
        ]);

        $teacher = $request->filled('teacher_id') ? User::where('uuid',$request->input('teacher_id'))->first() : null;


        $course = Course::create([...$request->only(['name', 'code', 'lms_id']),
                                  'teacher_id'=>$teacher?->id,]);

        return $course;
    }
}
