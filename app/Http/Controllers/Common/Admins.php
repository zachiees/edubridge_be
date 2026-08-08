<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class Admins extends Controller
{
    //
    public function index(Request $request){
        $query = User::where('role', 'admin');
        $page = $request->input('page', 1);
        $search = $request->input('query', '');
        $sort = $request->input('sort', '');
        $page_size = 20;

        if($search){
            $query->where(function($query) use ($search){
                $query->where('firstname', 'LIKE', "%$search%")
                      ->orWhere('middlename', 'LIKE', "%$search%")
                      ->orWhere('lastname', 'LIKE', "%$search%")
                      ->orWhere('email', 'LIKE', "%$search%");
            });
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
    public function destroy(string $uuid){
        return User::where('role', 'admin')->where('uuid', $uuid)->firstOrFail()->delete();
    }
}
