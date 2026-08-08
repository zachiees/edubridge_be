<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\StudentDetails;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Users extends Controller
{
    //
    public function import(Request $request){
        $request->validate([
            'items'=> 'required|array',
            'role' => 'required',
            'items.*.id'        => 'required',
            'items.*.username'  => 'required|string',
            'items.*.email'     => 'required',
            'items.*.firstname' => 'required|string',
            'items.*.lastname'  => 'required|string',
        ]);
        $role = $request->input('role');
        $items = $request->input('items');
        switch ($role) {
            case 'student': return $this->importStudents($items);
            case 'teacher': return $this->importTeachers($items);
            default: return response(['error'=>'Invalid target role']);
        }
    }
    //IMPORTS
    public function importStudents($items){
        foreach ($items as $student) {
            DB::beginTransaction();
            $user = User::updateOrCreate(
                ['email'     => $student['email']],
                ['firstname' => $student['firstname'],
                 'lastname'  => $student['lastname'],
                 'password'  => 'Student@2026',
                 'lms_id'    => $student['id'],
                 'role'      => 'student',
                ]);
            StudentDetails::updateOrCreate(
                ['user_id' => $user->id],
                ['lrn'     => $student['username']]
            );
            DB::commit();
        }
        return [];
    }
    public function importTeachers($items){

        return [];
    }
}
