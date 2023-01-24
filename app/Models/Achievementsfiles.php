<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Achievementsfiles extends Model
{
    use HasFactory;
    public function createAchievementFile($url,$achievementId){
        return $this->insert(['url'=>$url,'achievement_id'=>$achievementId]);
    }
    public function destroyAchievementFiles($id){
        Storage::deleteDirectory('public/achievements/achievement_'.str($id));
        return $this->where('achievement_id','=',$id)->delete();
    }
}
