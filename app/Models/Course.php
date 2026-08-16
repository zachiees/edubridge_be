<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

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

    protected $with = [];

    public function uniqueIds(){
        return ['uuid'];
    }
    //RELATIONS
    public function teacher(){
        return $this->belongsTo(User::class, 'teacher_id','id');
    }
    public function students(){
        return $this->belongsToMany(User::class, 'course_students', 'course_id', 'user_id');
    }

}
