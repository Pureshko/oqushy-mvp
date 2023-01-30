<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseApiFilesModel;

class LostFiles extends BaseApiFilesModel
{
    use HasFactory;
    protected $table = "lostfiles";
    public function createFile($url,$lostId){
        return $this->insert(['url'=>$url,'lost_id'=>$lostId]);
    }
    public function destroyFile($lostId){
        Storage::deleteDirectory('public/lost/lost_'.str($lostId));
        return $this->where('lost_id','=',$lostId)->delete();
    }
}
