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
    public function courses(){
        return $this->moodle->courses();
    }
}
