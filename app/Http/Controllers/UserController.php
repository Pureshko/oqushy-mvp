<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;

class UserController extends Controller
{
    /**
     * Receive data of user
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUser(Request $request){
        $token = $request->header('Authorization');
        $user = $this->User();
        $resp = $user->getUser($token);
        $result = [
            'status'=>(bool)$resp,
            'body'=>$resp
        ];
        return response()->json($result);
    }
    public function getUserById(Request $request,$id){
        $token = $request->header('Authorization');
        $user = $this->User();
        $resp = $user->getUserById($token,$id);
        $result = [
            'status'=>(bool)$resp,
            'body'=>$resp
        ];
        return response()->json($result);
    }
    public function User(){
        return new User();
    }
}
