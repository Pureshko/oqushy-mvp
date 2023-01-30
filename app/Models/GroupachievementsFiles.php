<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseApiFilesModel;

class GroupachievementsFiles extends BaseApiFilesModel
{
    use HasFactory;
    public function createFile($url,$id){
        return $this->insert(['url'=>$url,'groupachievement_id'=>$id]);
    }
    public function destroyFile($id){
        Storage::deleteDirectory('public/groupachievements/groupachievement_'.str($id));
        return $this->where('groupachievement_id','=',$id)->delete();
    }
}
