<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseApiModel;
use App\Models\LostFiles;

class Losts extends BaseApiModel
{
    use HasFactory;
    public function getList(){
        $losts = $this->join('users','users.id','=','losts.user_id')
                    ->select('losts.id','losts.name','users.name','losts.contact')
                    ->get()->toArray();
        $files = $this->BaseApiFilesModel()
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
    public function getUserList($token){
        $userId = $this->User()->getUserIdByToken($token);
        $losts = $this->join('users','users.id','=','losts.user_id')
                    ->where('users.id','=',$userId)
                    ->select('losts.id','losts.name','users.name','losts.contact')
                    ->get()->toArray();
        $files = $this->BaseApiFilesModel()
                    ->join('losts','losts.id','=','lostfiles.lost_id')
                    ->join('users','users.id','=','losts.user_id') 
                    ->where('users.id','=',$userId)
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
    public function getListByUserId($id){
        $losts = $this->join('users','users.id','=','losts.user_id')
                    ->where('users.id','=',$id)
                    ->select('losts.id','losts.name','users.name','losts.contact')
                    ->get()->toArray();
        $files = $this->BaseApiFilesModel()
                    ->join('losts','losts.id','=','lostfiles.lost_id')
                    ->join('users','users.id','=','losts.user_id')
                    ->where('users.id','=',$id)
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
    public function getById($id){
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
    public function updateLost($id,$data){
        return $this->where('id','=',$id)->update($data);
    }

    public function User(){
        return new User();
    }
    public function BaseApiFilesModel(){
        return new LostFiles();
    }
}
