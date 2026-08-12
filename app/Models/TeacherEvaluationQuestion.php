<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUniqueIds;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TeacherEvaluationQuestion extends Model
{   use HasUuids;
    //
    protected $hidden = ['id'];
    protected $fillable = [
        'evaluation_id',
        'type',
        'question',
    ];

    public function uniqueIds(){
        return ['uuid'];
    }

}
