<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Courses extends Controller
{
    //
    public function index(Request $request){
        $query = $request->user()->student_courses();
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

        //SORT
        match ($sort){
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc'=> $query->orderBy('name', 'desc'),
            default => null,
        };

        $count = $query->count();
        //PAGINATE
        $query->offset(($page - 1) * $page_size)->limit($page_size);
        $items = $query->get();

        return ['items'=>$items,'count'=>$count];
    }
}
