<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseApiFilesModel;
use Illuminate\Support\Facades\Storage;

class Achievementsfiles extends BaseApiFilesModel
{
    use HasFactory;
    public function createFile($url,$id){
        return $this->insert(['url'=>$url,'achievement_id'=>$id]);
    }
    public function destroyFile($id){
        Storage::deleteDirectory('public/achievements/achievement_'.str($id));
        return $this->where('achievement_id','=',$id)->delete();
    }
}
