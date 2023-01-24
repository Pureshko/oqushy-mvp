<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Achievements extends Model
{
    use HasFactory;
    public function createAchievement($array){
        return $this->insertGetId($array);
    }
    public function getUserAchievement($id){
        $user = $this->join('places','places.id','=','achievements.place_id')
                    ->join('categories','categories.id','=','places.category_id')
                    ->where('achievements.id','=',$id)
                    ->select(
                        'achievements.id',
                        'achievements.name',
                        'achievements.description',
                        'categories.name as category_name',
                        'places.place',
                        'places.score',
                        'achievements.status'
                    )
                    ->get()->toArray();
        if(!$user){
            return false;
        }
        $files = $this->join('achievementsfiles','achievementsfiles.achievement_id','=','achievements.id')
                    ->where('achievements.id','=',$id)
                    ->select('achievementsfiles.achievement_id','achievementsfiles.url')
                    ->get()->toArray();
        foreach($user as $key=>$us){
            foreach($files as $file){
                if($us['id'] == $file['achievement_id']){
                    $user[$key]['files'][] = $file['url'];
                }
            }
        }
        return $user;
    }
    public function getUserAchievementList($token){
        $id = $this->User()->getUserId($token);
        $achievements = $this->join('places','places.id','=','achievements.place_id')
                            ->where('achievements.owner','=',$id)
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
        $this->AchievementsFiles()->destroyAchievementFiles($id);
        return $this->where('id','=',$id)->delete();
    }
    public function createAchievementFiles($achievementId,$data){
        if($data['photo1']){
            $this->AchievementsFiles()->createAchievementFile($data['photo1'],$achievementId);
            if($data['photo2']){
                $this->AchievementsFiles()->createAchievementFile($data['photo2'],$achievementId);
                if($data['photo3']){
                    $this->AchievementsFiles()->createAchievementFile($data['photo3'],$achievementId);
                }else{
                    return true;
                }
            }else{
                return true;
            }
        }else{
            return false;
        }
    }
    public function User(){
        return new User();
    }
    public function AchievementsFiles(){
        return new Achievementsfiles();
    }
}
