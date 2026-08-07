<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Course extends Model
{
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

    public function teacher(){
        return $this->belongsTo(User::class, 'teacher_id','id');
    }

}
