<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\TeacherEvaluation;
use App\Models\TeacherEvaluationQuestion;
use App\Models\TeacherEvaluationRespondents;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Evaluations extends Controller
{
    //
    public function store(Request $request){
        $request->validate([
            'visible'     => 'required|boolean',
            'title'       => 'required',
            'reminders'   => 'nullable',
            'instructions'=> 'nullable',
            'scope' =>'required|in:all,grade_level,course,teacher',
            'scope_items' =>'array',
            'questions'         =>'array',
            'feedback_questions'=>'array',
        ]);
        //PREPARATIONS

        $scope           = $request->input('scope');
        $scope_items     = $request->input('scope_items',[]);
        $questions       = $request->input('questions',[]);
        $feedback_questions = $request->input('feedback_questions',[]);


        DB::beginTransaction();
        //STORE META DATA
        $eval = TeacherEvaluation::create([
            ...$request->except(['scope_items','questions','feedback_questions']),
        ]);

        foreach ($questions as $q){
            TeacherEvaluationQuestion::create(['evaluation_id'=>$eval->id,
                                               'type'         =>'rating',
                                               'question'     =>$q ]);
        }
        foreach ($feedback_questions as $q){
            TeacherEvaluationQuestion::create(['evaluation_id'=>$eval->id,
                                                'type'         =>'feedback',
                                                'question'     =>$q ]);
        }
        //POPULATE RESPONDENTS
        switch ($scope){
            case 'all':
                $this->eval_all($eval);
                break;
            case 'course':
                $this->eval_course($eval,$scope_items);
                break;
            case 'teacher':
                $this->eval_teacher($eval,$scope_items);
                break;
            default: abort(Response::HTTP_BAD_REQUEST);
        }
        DB::commit();
        return $eval;

    }
    private function eval_all(TeacherEvaluation $eval){
        //GET ALL COURSES
        $courses = Course::whereNotNull('teacher_id')->get();

        foreach ($courses as $c){
            //GET COURSE TEACHER
            $teacher = $c->teacher;
            $students = $c->students;
            //SKIP IF NULL
            if(!$teacher){
                continue;
            }
            //CREATE RESPONDENTS
            foreach ($students as $s){
                TeacherEvaluationRespondents::create([
                    'evaluation_id' => $eval->id,
                    'course_id'     => $c->id,
                    'teacher_id'    => $teacher->id,
                    'student_id'    => $s->id,
                ]);
            }
        }
    }
    private function eval_grade_level_respondents(TeacherEvaluation $eval,$grade_levels){

    }
    private function eval_course(TeacherEvaluation $eval,$course_list){
        Log::info('here');
        Log::info($course_list);
        foreach ($course_list as $uuid){
            //FIND COURSE
            $course = Course::where('uuid',$uuid)->first();
            if(!$course){
                continue;
            }
            //SKIP IF NO TEACHER
            if(!$course->teacher){
                continue;
            }
            //GET STUDENTS
            $students = $course->students;
            //CREATE RESPONDENTS
            foreach ($students as $s){
                TeacherEvaluationRespondents::create([
                    'evaluation_id' => $eval->id,
                    'course_id'     => $course->id,
                    'teacher_id'    => $course->teacher->id,
                    'student_id'    => $s->id,
                ]);
            }
        }
    }
    private function eval_teacher(TeacherEvaluation $eval,$teacher_list){
        foreach ($teacher_list as $uuid){
            //FIND TEACHER
            $teacher = User::where('uuid',$uuid)->first();
            //SKIP IF NULL
            if(!$teacher){
                continue;
            }
            //FIND TEACHER COURSES
            $courses = Course::where('teacher_id',$teacher->id)->get();
            foreach ($courses as $c){
                //GET STUDENTS
                $students = $c->students;
                foreach ($students as $s){
                    TeacherEvaluationRespondents::create([
                        'evaluation_id' => $eval->id,
                        'course_id'     => $c->id,
                        'teacher_id'    => $teacher->id,
                        'student_id'    => $s->id,
                    ]);
                }
            }
        }
    }
    //
    public function index(Request $request){
        $query = TeacherEvaluation::withCount(['respondents','questions']);
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
    public function find(Request $request,$uuid){
        return TeacherEvaluation::where('uuid',$uuid)->firstOrFail();
    }
    public function destroy(string $uuid){
        return TeacherEvaluation::where('uuid', $uuid)->firstOrFail()->delete();
    }
    public function set_visibility(Request $request, string $uuid){
        $request->validate(['visibility' => 'required|boolean']);
        $eval = TeacherEvaluation::where('uuid', $uuid)->firstOrFail();
        return $eval->update(['visible' => $request->input('visibility')]);
    }
    public function student_progress(Request $request, string $uuid){
        $eval = TeacherEvaluation::where('uuid', $uuid)->firstOrFail();

        return User::with(['student_evaluations','student_details'])
                        ->whereRelation('student_evaluations','evaluation_id',$eval->id)
                        ->get();
    }
    public function respondents(Request $request, string $uuid){
        return TeacherEvaluation::where('uuid',$uuid)->firstOrFail()->respondents;
    }

}
