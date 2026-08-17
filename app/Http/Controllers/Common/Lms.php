<?php

namespace App\Http\Controllers\Common;

use App\Classes\MoodleApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Lms extends Controller
{
    //
    public function __construct(private MoodleApi $moodle){
    }
    public function courses(Request $request){
        $course_id = $request->input('category_id',null);
        return $this->moodle->courses($course_id);
    }
    public function course_find(Request $request,$id){
        return $this->moodle->findCourseById($id);
    }
    public function course_categories(){
        return $this->moodle->courseCategories();
    }
    public function roles(){
        return $this->moodle->getRoles();
    }
    public function users_by_role(Request $request){
        $request->validate(['role_id'=>'required',
                            'context'=>'required']);

        $role_id = $request->input('role_id');
        $context = $request->input('context');

        return $this->moodle->getUserByRole($role_id, $context);
    }
}
