<?php

namespace App\Models;

use App\Http\Controllers\Common\Students;
use App\Models\TeacherEvaluation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TeacherEvaluationRespondents extends Model
{
    use HasUuids;

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

    protected $with = [];

    public function uniqueIds(){
        return ['uuid'];
    }

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
    public function evaluation(){
        return $this->belongsTo(TeacherEvaluation::class, 'evaluation_id', 'id');
    }
    public function responses(){
        return $this->hasMany(TeacherEvaluationResponse::class, 'respondent_id', 'id');
    }

}
