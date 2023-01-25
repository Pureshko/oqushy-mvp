<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ScoreController extends Controller
{
    public function getUserRankedList(Request $request){
        $grade = 0;
        $user = $this->User();
        if($request->has('grade')){
            $grade = $request->input('grade');   
            $resp = $user->getStudentsRankedList($grade,0);
            return $resp;
        }else if($request->has('gradeLow')&&$request->has('gradeHigh')){
            $gradeLow = $request->input('gradeLow');
            $gradeHigh = $request->input('gradeHigh');
            $resp = $user->getStudentsRankedList([$gradeLow,$gradeHigh],0);
            return $resp;
        }
        $resp = $user->getStudentsRankedList($grade,0);
        return $resp;
    }
    public function getGradesRankedList(){
        $list = $this->User()->getGradesRankedList();
        return response()->json($list);
    }
    public function getShanyraqRankedList(){
        $list = $this->User()->getShanyraqRankedList();
        return response()->json($list);
    }
    public function User(){
        return new User();
    }
}
