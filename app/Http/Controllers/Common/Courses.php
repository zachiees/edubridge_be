<?php

namespace App\Http\Controllers\Common;

use App\Classes\MoodleApi;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;

class Courses extends Controller
{
    //
    public function __construct(private MoodleApi $moodle){
    }
    public function store(Request $request){
        $request->validate([
            'name'       => 'required|string|max:200',
            'code'       => 'required|string|max:100',
            'teacher_id' => ['nullable','exists:users,uuid'],
            'lms_id'     => 'nullable|integer|unique:App\Models\Course,lms_id',
        ]);

        $teacher = $request->filled('teacher_id') ? User::where('uuid',$request->input('teacher_id'))->first() : null;


        $course = Course::create([...$request->only(['name', 'code', 'lms_id']),
                                  'teacher_id'=>$teacher?->id,]);

        return $course;
    }
    public function index(Request $request){
        $query = Course::withCount(['students']);
        $page = $request->input('page', 1);
        $search = $request->input('query', '');
        $sort = $request->input('sort', '');

        $page_size = 20;

        if($search){
            $query->where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")
                      ->orWhere('code', 'LIKE', "%$search%");
            });
        }

        //FILTERS


        //SORT
        match ($sort){
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc'=> $query->orderBy('name', 'desc'),
            'date_desc'=> $query->orderBy('created_at', 'desc'),
            'date_asc' => $query->orderBy('created_at', 'asc'),
            default => null,
        };

        $count = $query->count();
        //PAGINATE
        $query->offset(($page - 1) * $page_size)->limit($page_size);
        $items = $query->get();

        return ['items'=>$items,'count'=>$count];
    }
    public function find(Request $request, $uuid){
        return Course::with(['students.student_details','teacher'])->where('uuid',$uuid)->firstOrFail();
    }
    public function update(Request $request, string $uuid){
        $course = Course::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'name'       => 'required|string|max:200',
            'code'       => 'required|string|max:100',
            'teacher_id' => 'nullable|exists:users,uuid',
            'lms_id'     => ['nullable','integer',Rule::unique('courses','lms_id')->ignore($course->id,'id')],
        ]);

        $teacher = $request->filled('teacher_id') ? User::where('uuid',$request->input('teacher_id'))->first() : null;

        $course->update([...$request->only(['name', 'code', 'lms_id']),
                         'teacher_id'=>$teacher?->id,]);

        return $course;
    }
    public function destroy(string $uuid){
        return Course::where('uuid', $uuid)->firstOrFail()->delete();
    }
    public function list(){
        return Course::select(['uuid', 'name', 'code'])
                      ->orderBy('name', 'asc')
                      ->get();
    }
    public function add_students(Request $request,$uuid){
        $request->validate(['students'        => 'required|array',
                            'students.*.uuid' => 'required']);
        $items = $request->input('students',[]);

        $record = Course::where('uuid',$uuid)->firstOrFail();

        foreach($items as $student){
            $student = User::where('uuid',$student['uuid'])->firstOrFail();

            if(!$student) continue;

            $exists = CourseStudent::where('user_id', $student->id)
                                    ->where('course_id', $record->id)
                                    ->exists();

            if($exists) continue;

            CourseStudent::create([ 'user_id' => $student->id, 'course_id' => $record->id ]);
        }
        return [];
    }
    public function remove_student(Request $request, $uuid, $student_uuid){
        $record = Course::where('uuid', $uuid)->firstOrFail();
        $student = User::where('uuid', $student_uuid)->firstOrFail();

        CourseStudent::where('user_id', $student->id)
                      ->where('course_id', $record->id)
                      ->delete();

        return [];
    }
    public function import(Request $request){
        $request->validate([
            'course_id'          => 'required',
            'student_role_id'    => 'nullable',
            'teacher_role_id'    => 'nullable',
        ]);

        $course_id       = $request->input('course_id');
        $student_role_id = $request->input('student_role_id',null);
        $teacher_role_id = $request->input('teacher_role_id',null);
        $import_student  = $student_role_id!= null;
        $import_teachers = $teacher_role_id!= null;


        $lms_course = $this->moodle->findCourseById($course_id);
        $users      = $this->moodle->courseUsers($course_id);
        DB::beginTransaction();
        $local_course =    Course::updateOrCreate(['lms_id' => $course_id],
                                                  ['name'   => $lms_course['fullname'],
                                                   'code'   => $lms_course['shortname']]);

        if($import_student || $import_teachers){
            $users = $this->moodle->courseUsers($course_id);
            foreach($users as $u){
                if($import_student){
                    $this->importStudent($u,$student_role_id,$local_course);
                }
                if($import_teachers){
                    $this->importTeacher($u,$teacher_role_id,$local_course);
                }
            }
        }
        DB::commit();
        return [];
    }
    private function importStudent($user,$student_role_id,$local_course){
        $user_roles = $user['roles'];
        //FIND IF USER HAS CORRECT ROLE
        $role = array_find($user_roles, function($role) use ($student_role_id){ return $role['roleid'] == $student_role_id; });
        if(!$role){
            return;
        }
        //FIND LOCAL USER
        $local_user = User::where('lms_id',$user['id'])->first();
        if(!$local_user){
            return;
        }
        $exists = CourseStudent::where('user_id', $local_user->id)
                                ->where('course_id', $local_course->id)
                                ->exists();
        if($exists){
            return;
        }
        CourseStudent::create(['user_id' => $local_user->id,
                               'course_id' => $local_course->id ]);

    }
    private function importTeacher($user,$teacher_role_id,$local_course){
        $user_roles = $user['roles'];
        //FIND IF USER HAS CORRECT ROLE
        $role = array_find($user_roles, function($role) use ($teacher_role_id){ return $role['roleid'] == $teacher_role_id; });
        if(!$role){
            return;
        }
        //FIND LOCAL USER
        $local_user = User::where('lms_id',$user['id'])->first();
        if(!$local_user){
            return;
        }
        $local_course->update(['teacher_id' => $local_user->id]);
    }
    //

}
