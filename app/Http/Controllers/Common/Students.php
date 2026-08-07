<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\StudentDetails;

class Students extends Controller
{
    //
    public function store(Request $request){
        $request->validate([
            'firstname'   => ['required', 'string', 'max:100'],
            'middlename'  => ['nullable', 'string', 'max:50'],
            'lastname'    => ['required', 'string', 'max:100'],
            'suffix'      => ['nullable', 'string', 'max:10'],
            'mobile'      => ['required', 'string'],
            'address'     => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:100','unique:users,email'],
            'lrn'         => ['required', 'digits:12','unique:App\Models\StudentDetails,lrn'],
            'payee_type'  => ['required', 'string', Rule::in(['payee', 'peac', 'esc'])],
            'grade_level' => ['required', 'string'],
            'modality'    => ['required', 'string', Rule::in(['face_to_face', 'hybrid', 'online'])],
        ]);
        DB::beginTransaction();
        $password = uniqid('', true);
        $user = User::create([...$request->only(['firstname',
                                                 'middlename',
                                                 'lastname',
                                                 'suffix',
                                                 'mobile',
                                                 'address',
                                                 'email']),
                                'role'=>'student',
                                'password' => $password]);
        StudentDetails::create([...$request->only(['lrn','payee_type','grade_level','modality']),
                                'user_id' => $user->id,]);
        DB::commit();
        $user->load('student_details');
        return $user;

    }
    public function index(Request $request){
        $query = User::with('student_details')->where('role', 'student');
        $page = $request->input('page', 1);
        $search = $request->input('query', '');
        $sort = $request->input('sort', '');
        $grade_level = $request->input('grade_level', '');
        $page_size = 20;

        if($search){
            $query->where(function($query) use ($search){
                $query->where('firstname', 'LIKE', "%$search%")
                      ->orWhere('middlename', 'LIKE', "%$search%")
                      ->orWhere('lastname', 'LIKE', "%$search%")
                      ->orWhere('email', 'LIKE', "%$search%")
                      ->orWhereRelation('student_details', 'lrn', 'LIKE', "%$search%");
            });
        }



        //FILTERS
        if($grade_level){
            $query->whereRelation('student_details','grade_level', $grade_level);
        }

        //SORT
        match ($sort){
            'name_asc' => $query->orderBy('firstname', 'asc'),
            'name_desc'=> $query->orderBy('firstname', 'desc'),
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
}
