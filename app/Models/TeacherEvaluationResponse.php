<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherEvaluationResponse extends Model
{
    //
    protected $hidden = [
        'id',
        'evaluation_id',
        'respondent_id',
        'question_id',
    ];

    protected $fillable = [
        'evaluation_id',
        'respondent_id',
        'question_id',
        'rating',
        'response'
    ];



}
