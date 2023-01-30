<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseAchievementModel;


class Groupachievements extends BaseApiModel
{
    use HasFactory;
    public function getList($token){
        return $this->join('places','places.id','=','groupachievements.place_id')
                    ->join('shanyraqs','shanyraqs.id','=','groupachievements.shanyraq_id')
                    ->join('studentgrade','studentgrade.grade_id','=','shanyraq.grade_id')
                    ->join('users','users.id','=','studentgrade.student_id')
                    ->where('users.apiKey','=',$token)
                    ->select('groupachievements.id','groupachievements.name','groupachievements.description','groupachievements.status', 'places.score')->get()->toArray();
    }
    public function getListByUserId($id)
    {
        return $this->join('places','places.id','=','groupachievements.place_id')
                    ->join('shanyraqs','shanyraqs.id','=','groupachievements.shanyraq_id')
                    ->join('studentgrade','studentgrade.grade_id','=','shanyraq.grade_id')
                    ->join('users','users.id','=','studentgrade.student_id')
                    ->where('users.id','=',$id)
                    ->select('groupachievements.id','groupachievements.name','groupachievements.description','groupachievements.status', 'places.score')->get()->toArray();
    }
    public function getById($id)
    {
        return $this->join('places','places.id','=','groupachievements.place_id')
                    ->join('shanyraqs','shanyraqs.id','=','groupachievements.shanyraq_id')
                    ->where('groupachievements.id','=',$id)
                    ->select('groupachievements.id','groupachievements.name','groupachievements.description','shanyraqs.name','groupachievements.status', 'places.score')->get()->toArray();
    }
    public function BaseApiFilesModel(){
        return new GroupAchievementsFiles();
    }
    public function User(){
        return new User();
    }
}
