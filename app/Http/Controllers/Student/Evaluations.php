<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TeacherEvaluation;
use App\Models\TeacherEvaluationRespondents;
use Illuminate\Http\Request;

class Evaluations extends Controller
{
    //
    public function index(Request $request){
        $user = $request->user();
        $query = TeacherEvaluationRespondents::where('student_id',$user->id);

        $page = $request->input('page', 1);
        $status = $request->input('status', '');
        $page_size = 20;

        //FILTERS
        if($status){
            $query->where('status', $status);
        }

        $count = $query->count();
        //PAGINATE
        $query->offset(($page - 1) * $page_size)->limit($page_size);
        $items = $query->get();

        return ['items'=>$items,'count'=>$count];
    }
    public function find(Request $request, $uuid){
        $user = $request->user();
        return TeacherEvaluationRespondents::with(['evaluation.questions'])
                                            ->where('uuid',$uuid)
                                            ->where('student_id',$user->id)
                                            ->firstOrFail();
    }
}
