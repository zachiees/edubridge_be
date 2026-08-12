<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\TeacherEvaluation;
use App\Models\TeacherEvaluationQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Evaluations extends Controller
{
    //
    public function store(Request $request){
        $request->validate([
            'scope' =>'required|in:all,grade_level,course,teacher',
            'scope_items' =>'array',
            'questions'         =>'array',
            'feedback_questions'=>'array',
            'format'            =>'nullable',
        ]);
        //PREPARATIONS

        $scope           = $request->input('scope');
        $scope_items     = $request->input('scope_items',[]);
        $questions       = $request->input('questions',[]);
        $feedback_questions = $request->input('feedback_questions',[]);
        $format = $request->input('format',[]);


        DB::beginTransaction();
        //STORE META DATA
        $eval = TeacherEvaluation::create([
            'scope' =>$scope,
            'format'=>$format,
        ]);

        foreach ($questions as $q){
            TeacherEvaluationQuestion::create(['evaluation_id'=>$eval->id,
                                               'type'         =>'rating',
                                               'question'     =>$q ]);
        }
        DB::commit();
        return $eval;

    }
    public function index(Request $request){
        $query = TeacherEvaluation::with('questions');
        $page = $request->input('page', 1);
        $scope = $request->input('scope', '');
        $sort = $request->input('sort', '');
        $page_size = 20;

        //FILTERS
        if($scope){
            $query->where('scope', $scope);
        }

        //SORT
        match ($sort){
            'date_desc'=> $query->orderBy('created_at', 'desc'),
            'date_asc' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $count = $query->count();
        //PAGINATE
        $query->offset(($page - 1) * $page_size)->limit($page_size);
        $items = $query->get();

        return ['items'=>$items,'count'=>$count];
    }
}
