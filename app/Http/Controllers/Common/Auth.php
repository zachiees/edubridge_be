<?php

namespace App\Http\Controllers\Common;

use App\Classes\MoodleApi;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as Authenticator;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Auth extends Controller
{
    //

    public function __construct(public MoodleApi $moodle){
    }

    public function login(Request $request){
        $request->validate([ 'email' => 'required|max:100',
                             'password' => 'required|max:20']);
        $email_username = $request->input('email');
        $password       = $request->input('password');

        $authenticated = false;

        if(Authenticator::attempt(['email' => $email_username, 'password' => $password])){
           $authenticated = true;
        }
        if(!$authenticated && $this->moodle->login($email_username, $password)){
            //FIND LOCAL USER
            $user = User::where('email',$email_username)
                        ->orWhereRelation('student_details','lrn',$email_username)
                        ->first();
            if($user){
                $user->update([ 'password' => $password ]);
                $authenticated = true;
                Authenticator::login($user);
            }
        }

        if(!$authenticated){
            return response(['error' => "Unauthorized"], Response::HTTP_UNAUTHORIZED);
        }

        $user = Authenticator::user();
        $api_token = $user->createToken('api_token');
        $token = $api_token->plainTextToken;

        return ['user' => $user,
                'token' => $token];
    }
    public function logout(Request $request){
        return $request->user()->currentAccessToken()->delete();
    }
    public function validate(){
        return [];
    }
}
