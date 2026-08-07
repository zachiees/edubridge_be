<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

        return ['ok'];

    }
}
