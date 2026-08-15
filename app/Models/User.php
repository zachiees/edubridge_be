<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use http\Env\Response;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasUuids,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'username',
        'firstname',
        'middlename',
        'lastname',
        'suffix',
        'mobile',
        'address',
        'email',
        'password',
        'role',
        'has_lms',
        'lms_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function uniqueIds(){
        return ['uuid'];
    }
    //RELATIONS
    public function student_details(){
        return $this->hasOne(StudentDetails::class,'user_id','id');
    }
    public function student_courses(){
        return $this->belongsToMany(Course::class, 'course_students', 'user_id', 'course_id');
    }
    public function student_evaluations(){
        return $this->hasMany(TeacherEvaluationRespondents::class, 'student_id');
    }
}
