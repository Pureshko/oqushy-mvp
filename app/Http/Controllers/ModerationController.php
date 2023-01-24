<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievements;
use Illuminate\Support\Facades\Validator;

class ModerationController extends Controller
{
    public function getNonModeratedAchievements(Request $request){
        $token = $request->header('Authorization');
        return response()->json($this->Achievements()->getNonModeratedAchievements($token));
    }
    public function acceptOrDeclineAchievement($id){
        $input = request()->all();
        $validator = Validator::make($input, [
            'accept' => 'required|boolean',
        ]);
        if($validator->fails()){
            return response()->json(['success'=>false,'response'=>$validator->errors()],400);
        }
        return response()->json(['success'=>(bool)$this->Achievements()->acceptOrDeclineAchievement($id,$input['accept'])]);
    }
    public function Achievements(){
        return new Achievements();
    }
}
