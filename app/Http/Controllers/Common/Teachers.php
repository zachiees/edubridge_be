<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Teachers extends Controller
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
                                'role'=>'teacher',
                                'password' => $password]);
        DB::commit();
        return $user;

    }
    public function index(Request $request){
        $query = User::where('role', 'teacher');
        $page = $request->input('page', 1);
        $search = $request->input('query', '');
        $page_size = 20;


        $count = $query->count();
        //PAGINATE
        $query->offset(($page - 1) * $page_size)->limit($page_size);
        $items = $query->get();

        return ['items'=>$items,'count'=>$count];
    }
    public function list(){
        return User::select(['uuid', 'firstname', 'middlename', 'lastname', 'email'])
                    ->where('role', 'teacher')
                    ->orderBy('firstname', 'asc')
                    ->get();
    }
}
