<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as Authenticator;
use Symfony\Component\HttpFoundation\Response;

class Auth extends Controller
{
    //
    public function login(Request $request){
        $request->validate([ 'email' => 'required|max:100',
                             'password' => 'required|max:20']);
        $email_username = $request->input('email');
        $password       = $request->input('password');

        if(!Authenticator::attempt(['email' => $email_username, 'password' => $password]) &&
           !Authenticator::attempt(['username' => $email_username, 'password' => $password])){
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
