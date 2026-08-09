<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseStudent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;

class Courses extends Controller
{
    //
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
        $query = Course::query();
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
        return Course::with(['students','teacher'])->where('uuid',$uuid)->firstOrFail();
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
    public function import(Request $request){
        $request->validate([
            'items'              => 'required|array',
            'items.*.id'         => 'required',
            'items.*.shortname'  => 'required|string',
            'items.*.fullname'   => 'required|string',
        ]);

        $items = $request->input('items');

        foreach ($items as $course) {
            Course::updateOrCreate(
                ['lms_id' => $course['id']],
                ['name'   => $course['fullname'],
                 'code'   => $course['shortname']]
            );
        }

        return [];
    }
}
