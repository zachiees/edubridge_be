<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Course;
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
    public function destroy(string $uuid){
        return Course::where('uuid', $uuid)->firstOrFail()->delete();
    }
    public function list(){
        return Course::select(['uuid', 'name', 'code'])
                      ->orderBy('name', 'asc')
                      ->get();
    }
}
