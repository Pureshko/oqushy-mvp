<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LostFiles;
use Illuminate\Support\Facades\DB;

class Losts extends Model
{
    use HasFactory;
    public function getLostList(){
        $losts = $this->join('users','users.id','=','losts.user_id')
                    ->select('losts.id','losts.name','users.name','losts.contact')
                    ->get()->toArray();
        $files = $this->join('lostfiles','lostfiles.lost_id','=','losts.id')
                    ->groupBy('lostfiles.lost_id','lostfiles.url')
                    ->select('lostfiles.lost_id','lostfiles.url')
                    ->get()->toArray();
        foreach($losts as $key=>$lost){
            foreach($files as $file){
                if($lost['id'] == $file['lost_id']){
                    $losts[$key]['files'][] = $file['url'];
                }
            }
        }
        return $losts;
    }
    public function getLost($id){
        $lost = $this->join('users','users.id','=','losts.user_id')
                    ->where('losts.id','=',$id)
                    ->select('losts.*','users.name')
                    ->get()->toArray();
        $files = $this->join('lostfiles','lostfiles.lost_id','=','losts.id')
                    ->where('lostfiles.lost_id','=',$id)
                    ->select('lostfiles.url')
                    ->get()->toArray();
        foreach($lost as $key=>$lost){
            foreach($files as $file){
                if($lost['id'] == $file['lost_id']){
                    $lost[$key]['files'][] = $file['url'];
                }
            }
        }
        return $lost;
    }
    public function destroyLost($id){
        $this->join('lostfiles','lostfiles.lost_id','=','losts.id')
            ->where('lostfiles.lost_id','=',$id)
            ->delete();
        return $this->where('id','=',$id)->delete();
    }
    public function updateLost($id,$data){
        return $this->where('id','=',$id)->update($data);
    }
    public function createLost($data){
        return $this->insertGetId($data);
    }
    public function createLostFiles($lostId,$data){
        if($data['photo1']){
            $this->LostFiles()->createLostFile($data['photo1'],$lostId);
            if($data['photo2']){
                $this->LostFiles()->createLostFile($data['photo2'],$lostId);
                if($data['photo3']){
                    $this->LostFiles()->createLostFile($data['photo3'],$lostId);
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
    public function LostFiles(){
        return new LostFiles();
    }
}
