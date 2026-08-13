<?php

namespace App\Models;

use App\Http\Controllers\Common\Students;
use Illuminate\Database\Eloquent\Model;

class TeacherEvaluationRespondents extends Model
{
    //
    protected $hidden = [
        'id',
        'evaluation_id',
        'student_id',
        'teacher_id',
        'course_id'
    ];
    protected $fillable = [
        'evaluation_id',
        'student_id',
        'teacher_id',
        'course_id',
        'status',
    ];

    protected $with = ['student','teacher','course'];

    //RELATIONS
    public function student(){
        return $this->belongsTo(User::class, 'student_id', 'id');
    }
    public function teacher(){
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }
    public function course(){
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

}
