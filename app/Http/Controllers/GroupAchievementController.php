<?php

namespace App\Http\Controllers;

use App\Models\Groupachievements;

class GroupAchievementController extends ApiController
{
    protected $insertValidateArray = [
        'name'=>'required|max:255',
        'description'=>'required|max:10000',
        'place_id'=>'required|integer',
        'date'=>'required|date',
        'factor'=>'required|integer',
        'photo1'=>'nullable|image',
        'photo2'=>'nullable|image',
        'photo3'=>'nullable|image',
    ];
    protected $updateValidateArray=[
        'name'=>'required|max:255',
        'description'=>'required|max:10000',
        'place_id'=>'required|integer',
        'date'=>'required|date',
        'factor'=>'required|integer',
        'photo1'=>'nullable|image',
        'photo2'=>'nullable|image',
        'photo3'=>'nullable|image',
    ];
    protected $dataArray=[
        'name',
        'description',
        'place_id',
        'date',
        'factor',
        'shanyraq_id',
        'status'=>"NEW",
    ];
    protected $modelName = 'Groupachievements';
    protected $modelIdName = 'groupachievement_id';
    protected $path = 'achievements/group/achievement_';
    protected $defaultPath = 'achievements/default.jpg';
    public function BaseApiModel(){
        return new Groupachievements();
    }
}
