<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TeacherEvaluation extends Model
{   use HasUuids;
    //
    protected $hidden = ['id'];
    protected $fillable = [
        'scope',
        'format',
    ];
    protected $casts = ['format' => 'array'];
    public function uniqueIds(){
        return ['uuid'];
    }

    public function questions(){
        return $this->hasMany(TeacherEvaluationQuestion::class, 'evaluation_id', 'id');
    }
    public function respondents(){
        return $this->hasMany(TeacherEvaluationRespondents::class, 'evaluation_id', 'id');
    }

}
