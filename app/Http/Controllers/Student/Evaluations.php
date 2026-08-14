<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TeacherEvaluation;
use App\Models\TeacherEvaluationQuestion;
use App\Models\TeacherEvaluationRespondents;
use App\Models\TeacherEvaluationResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
    public function submit(Request $request,$uuid){
        $user = $request->user();
        $respondent = TeacherEvaluationRespondents::with(['evaluation.questions'])
                                                ->where('uuid',$uuid)
                                                ->where('student_id',$user->id)
                                                ->firstOrFail();

        if($respondent->status == 'completed'){
            return response(['message'=>'item already completed'],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $eval    = $respondent->evaluation;
        $teacher = $respondent->teacher;
        $course  = $respondent->course;
        $student = $respondent->student;

        $responses = $request->all();
        DB::beginTransaction();
        foreach($responses as $key => $value){
           $question = TeacherEvaluationQuestion::where('uuid',$key)->first();
           TeacherEvaluationResponse::create([
               'evaluation_id'=> $eval->id,
               'respondent_id'=> $respondent->id,
               'question_id'  => $question->id,
               'rating'       => $question->type == 'rating' ?  $value : 0,
               'response'     => $question->type == 'feedback'? $value : null
           ]);
        }
        $respondent->update(['status' => 'completed']);
        DB::commit();
        return [];
    }
}
