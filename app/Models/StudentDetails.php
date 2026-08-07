<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDetails extends Model
{
    //
    protected $hidden = ['id','user_id'];
    protected $fillable = ['user_id',
                           'lrn',
                           'grade_level',
                           'modality',
                           'payee_type'];

}
