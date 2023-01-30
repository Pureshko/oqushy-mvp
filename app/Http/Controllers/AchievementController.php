<?php

namespace App\Http\Controllers;

use \App\Models\Achievements;


class AchievementController extends ApiController
{
    protected $insertValidateArray = [
        'name'=>'required|max:255',
        'description'=>'required|max:10000',
        'place_id'=>'required|integer',
        'supervisor_id'=>'nullable|integer',
        'date'=>'required|date',
        'rating'=>'nullable|boolean',
        'photo1'=>'nullable|image',
        'photo2'=>'nullable|image',
        'photo3'=>'nullable|image',
    ];
    protected $updateValidateArray=[
        'name'=>'required|max:255',
        'description'=>'required|max:10000',
        'place_id'=>'required|integer',
        'supervisor_id'=>'nullable|integer',
        'date'=>'required|date',
        'photo1'=>'nullable|image',
        'photo2'=>'nullable|image',
        'photo3'=>'nullable|image',
    ];
    protected $dataArray=[
        'name',
        'description',
        'place_id',
        'date',
        'owner',
        'status'=>"NEW",
        'rating'=>1,
    ];
    protected $modelName = 'Achievements';
    protected $modelIdName = 'achievement_id';
    protected $path = 'achievements/achievement_';
    protected $defaultPath = 'achievements/default.jpg';
    protected function bindSupervisor($inputs,$achievementId){
        if(isset($inputs['supervisor'])){
            $this->BaseApiModel()->bindAchievementSupervisor($achievementId,$inputs['supervisor']);
        }
    }
    public function BaseApiModel(){
        return new Achievements();
    }
}
