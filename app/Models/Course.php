<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Course extends Model
{
    use HasUuids;

    //
    protected $hidden = [
        'id',
        'teacher_id',
    ];
    protected $fillable = [
        'name',
        'code',
        'teacher_id',
        'lms_id',
    ];

    protected $with = [
        'teacher'
    ];

    public function uniqueIds(){
        return ['uuid'];
    }

    public function teacher(){
        return $this->belongsTo(User::class, 'teacher_id','id');
    }

}
