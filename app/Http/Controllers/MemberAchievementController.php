<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\MemberAchievements;

class MemberAchievementController extends ApiController
{
    protected $modelName = 'MemberAchievements';
    protected $modelIdName = 'memberachievement_id';
    protected $insertValidateArray = [
        'student_id' => 'required',
        'title' => 'required',
        'description' => 'required',
        'date' => 'required',
        'place_id' => 'required',
        'status' => 'required',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $inputs = $request->all();
        if($inputs['user_id']){
            $response = $this->BaseApiModel()->getListByUserId($inputs['user_id']);
            if(empty($response)){
                $result = [
                    'status'=>false,
                    'message'=>'No '.strtolower($this->modelName).' found'
                ];
                return response()->json($result,404);
            }
            $result = [
                'status' => (bool)$response,
                'response'=> $response
            ];
            return response()->json($result);
        }else if($inputs['shanyraq_id']){
            $response = $this->BaseApiModel()->getListByShanyraqId($inputs['shanyraq_id']);
            if(empty($response)){
                $result = [
                    'status'=>false,
                    'message'=>'No '.strtolower($this->modelName).' found'
                ];
                return response()->json($result,404);
            }
        }
        $response = $this->BaseApiModel()->getList();
        if(empty($response)){
            $result = [
                'status'=>false,
                'message'=>'No '.strtolower($this->modelName).' found'
            ];
            return response()->json($result,404);
        }
        $result = [
            'status' => (bool)$response,
            'response'=> $response
        ];
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function BaseApiModel(){
        return new MemberAchievements();
    }
}
