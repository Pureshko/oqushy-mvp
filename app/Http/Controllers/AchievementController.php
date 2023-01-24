<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Achievements;
use \Illuminate\Support\Facades\Validator;

class AchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $achievement = $this->Achievements();
        $token = $request->header('Authorization');
        $response = $achievement->getUserAchievementList($token);
        $result = [
            'status'=>(bool)($response),
            'body'=>$response
        ];
        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $token = $request->header('Authorization');
        $inputs = $request->all();
        $credentials = Validator::make($inputs, [
            'name'=>'required|max:255',
            'description'=>'required|max:10000',
            'place_id'=>'required|max:255',
            'photo1'=>'nullable|image',
            'photo2'=>'nullable|image',
            'photo3'=>'nullable|image',
        ]);
        if($credentials->fails()){
            return response()->json([
                'status'=>false,
                'body'=>$credentials->errors()
            ],400);
        }
        $data = [
            'name'=>$inputs['name'],
            'description'=>$inputs['description'],
            'place_id'=>$inputs['place_id'],
            'owner'=>$this->Achievements()->User()->getUserId($token),
            'date'=>NULL,
            'status'=>'NEW'
        ];

        $achievementId = $this->Achievements()->createAchievement($data);
        if(isset($achievementId)){
            $photo1 = $request->hasFile('photo1') ? $request->file('photo1') : NULL;
            $photo2 = $request->hasFile('photo2') ? $request->file('photo2') : NULL;
            $photo3 = $request->hasFile('photo3') ? $request->file('photo3') : NULL;
            $photo1Name = $request->hasFile('photo1') ? $achievementId.'-1.'.$photo1->extension() : NULL;
            $photo2Name = $request->hasFile('photo2') ? $achievementId.'-2.'.$photo2->extension() : NULL;
            $photo3Name = $request->hasFile('photo3') ? $achievementId.'-3.'.$photo3->extension() : NULL;
            if(!$photo1 && !$photo2 && !$photo3){
                $photo1Name = 'achievements/default.jpg';
            }
            mkdir(storage_path('app/public/achievements/achievement_'.str($achievementId)));
            if($photo1){
                $photo1->move(storage_path('app/public/losts/achievement_'.str($achievementId)), $photo1Name);
                $photo1Name = 'achievements/achievement_'.str($achievementId).'/'.$photo1Name;
            }
            if($photo2){
                $photo2->move(storage_path('app/public/losts/achievement_'.str($achievementId)), $photo2Name);
                $photo2Name = 'achievements/achievement_'.str($achievementId).'/'.$photo2Name;
            }
            if($photo3){
                $photo3->move(storage_path('app/public/losts/achievement_'.str($achievementId)), $photo3Name);
                $photo3Name = 'achievements/achievement_'.str($achievementId).'/'.$photo3Name;
            }
            $succ = $this->Achievements()->createAchievementFiles($achievementId, [
                'photo1'=>$photo1Name,
                'photo2'=>$photo2Name,
                'photo3'=>$photo3Name,
            ]);
            if(!$succ){
                return response()->json([
                    'status'=>'error',
                    'message'=>'Achievement created successfully, but photos not uploaded',
                    'body'=>[
                        'achievement_id'=>$achievementId,
                    ],
                ]);
            }
            return response()->json([
                'status'=>'success',
                'message'=>'Achievement created successfully',
                'body'=>[
                    'achievement_id'=>$achievementId,
                ],
            ]);
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>'Achievement not created',
            ]);
        }
     
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $achievement = $this->Achievements();
        $response = $achievement->getUserAchievement($id);
        if($response){
            $result = [
                'status'=>(bool)($response),
                'body'=>$response[0]
            ];
            return response()->json($result);
        }else{
            $result = [
                'status'=>false,
                'message'=>"Achievement not found"
            ];
            return response()->json($result,404);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = [
            'status'=>(bool)($this->Achievements()->destroyAchievement($id))
        ];
        if($result['status']){
            $result['message'] = 'Achievement deleted successfully';
            return response()->json($result);
        }else{
            $result['message'] = 'Achievement not found';
            return response()->json($result,404);
        }
    }
    public function Achievements(){
        return new Achievements();
    }
}
