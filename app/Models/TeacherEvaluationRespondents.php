<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherEvaluationRespondents extends Model
{
    //
    protected $hidden = ['id'];
    protected $fillable = [
        'evaluation_id',
        'student_id',
        'teacher_id',
        'course_id'
    ];

}
