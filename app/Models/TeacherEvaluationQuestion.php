<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherEvaluationQuestion extends Model
{
    //
    protected $hidden = ['id'];
    protected $fillable = [
        'evaluation_id',
        'type',
        'question',
    ];


}
