<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseApiFilesModel;

abstract class BaseApiModel extends Model
{
    use HasFactory;
    abstract protected function getUserList($token);
    abstract protected function getListByUserId($id);
    abstract protected function getById($id);
    public function create($data){
        return $this->insertGetId($data);
    }
    public function createFiles($id,$data){
        if($data['photo1']){
            $this->BaseApiFilesModel()->createFile($data['photo1'],$id);
            if($data['photo2']){
                $this->BaseApiFilesModel()->createtFile($data['photo2'],$id);
                if($data['photo3']){
                    $this->BaseApiFilesModel()->createtFile($data['photo3'],$id);
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
    public function BaseApiFilesModel(){
        return new BaseApiFilesModel();
    }
}
