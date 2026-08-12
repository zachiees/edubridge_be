<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUniqueIds;
use Illuminate\Database\Eloquent\Model;

class TeacherEvaluation extends Model
{   use HasUniqueIds;
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

}
