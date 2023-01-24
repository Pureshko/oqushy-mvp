<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials)) {
            $id = Auth::id();
            $token = $this->User()->generateToken();
            $result = ["success"=>(bool)$this->User()->setToken($id,['apiKey'=>$token]),"response"=>['apiKey'=>$token,],];
            return response()->json($result,200);
        }
 
        return response()->json(['success'=>false, "response"=>"Email or password is incorrect"], 403);
    }
    public function User(){
        return new User();
    }
}
