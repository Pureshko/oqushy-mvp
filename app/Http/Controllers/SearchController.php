<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    //
    public function searchStudents(Request $request){
        $search = $request->input('search');
        $users = User::join('user_roles','user_roles.user_id','=','users.id')
                    ->join('roles','roles.id','=','user_roles.role_id')
                    ->where('roles.name','student')
                    ->where('name','like','%'.$search.'%')->get();
        return response()->json($users);
    }
    public function searchTeachers(Request $request){
        $search = $request->input('search');
        $users = User::join('user_roles','user_roles.user_id','=','users.id')
                    ->join('roles','roles.id','=','user_roles.role_id')
                    ->where('roles.name','teacher')
                    ->where('name','like','%'.$search.'%')->get();
        return response()->json($users);
    }
}
