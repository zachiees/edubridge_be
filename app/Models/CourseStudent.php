<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    //
    protected $hidden = ['id'];
    protected $fillable = ['user_id', 'course_id'];
}
