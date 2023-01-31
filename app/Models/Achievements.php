<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\AchievementSupervisors;
use App\Models\BaseApiModel;

class Achievements extends BaseApiModel
{
    use HasFactory;
    public function bindAchievementSupervisor($achievementId, $supervisorId){
        return AchievementSupervisors()->insertSuperivsor($achievementId, $supervisorId);
    }
    public function getById($id){
        $files = $this->BaseApiFilesModel()->where('achievement_id','=',$id)->select('url')->get()->toArray();
        $supervisors = $this->AchievementSupervisors()
                    ->join('users','users.id','=','achievement_supervisors.supervisor_id')
                    ->select('users.id','users.name')->get()->toArray();
        $user = $this->join('places','places.id','=','achievements.place_id')
                    ->join('subcategories','subcategories.id','=','places.subcategory_id')
                    ->join('categories','categories.id','=','subcategories.category_id')
                    ->where('achievements.id','=',$id)
                    ->groupBy('achievements.id',
                        'achievements.name',
                        'achievements.description',
                        'category_name',
                        'subcategory_name',
                        'places.place',
                        'places.score',
                        'achievements.status',
                    )
                    ->select(
                        'achievements.id',
                        'achievements.name',
                        'achievements.description',
                        'categories.name as category_name',
                        'subcategories.name as subcategory_name',
                        'places.place',
                        'places.score',
                        'achievements.status',
                    )
                    ->get()->toArray();
        if(!$user){
            return false;
        }
        $user[0]['files'] = \array_column($files,'url');
        $user[0]['supervisors'] = $supervisors;
        return $user;
    }
    public function getListByUserId($id){
        $achievements = $this->join('places','places.id','=','achievements.place_id')
                            ->join('users','users.id','=','achievements.owner')
                            ->where('users.id','=',$id)
                            ->select('achievements.id','achievements.name','achievements.description','achievements.status','places.score')
                            ->get()->toArray();
        return $achievements;
    }
    public function getUserList($token){
        $achievements = $this->join('places','places.id','=','achievements.place_id')
                            ->join('users','users.id','=','achievements.owner')
                            ->where('users.apiKey','=',$token)
                            ->select('achievements.id','achievements.name','achievements.description','achievements.status','places.score')
                            ->get()->toArray();
        return $achievements;
    }
    public function getNonModeratedAchievements($token){
        $gradeId = $this->User()->getStudentGradeId($token);
        return $this->join('studentgrade','studentgrade.user_id','=','achievements.owner')
                    ->join('places','places.id','=','achievements.place_id')
                    ->where('studentgrade.grade_id','=',$gradeId)
                    ->where('status','=','NEW')
                    ->select('achievements.id','achievements.name','achievements.description','places.score')
                    ->get()->toArray();
    }
    public function acceptOrDeclineAchievement($id,$status){
        return ($status)? $this->where('id','=',$id)
                        ->update(['status'=>'ACC']) :
                        $this->where('id','=',$id)->update(['status'=>'DEC']);
    }
    public function destroyAchievement($id){
        $this->BaseApiFilesModel()->destroyAchievementFiles($id);
        return $this->where('id','=',$id)->delete();
    }
    public function User(){
        return new User();
    }
    public function BaseApiFilesModel(){
        return new Achievementsfiles();
    }
    public function AchievementSupervisors(){
        return new AchievementSupervisors();
    }
}
