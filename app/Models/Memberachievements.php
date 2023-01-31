<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseApiModel;

class Memberachievements extends BaseApiModel
{
    use HasFactory;
    public function getById($id)
    {
        $memberachievement = $this->join('users','users.id','=','memberachievements.student_id')
                                ->join('studentgrades','studentgrades.id','=','memberachievements.studentgrade_id')
                                ->join('shanyraqs','shanyraqs.id','=','memberachievements.shanyraq_id')
                                ->join('places','places.id','=','memberachievements.place_id')
                                ->join('subcategories','subcategories.id','=','places.subcategory_id')
                                ->join('categories','categories.id','=','subcategories.category_id')
                                ->where('memberachievements.id',$id)
                                ->select('memberachievements.id','users.name as student_name','places.name as place_name','subcategories.name as subcategory_name','categories.name as category_name','memberachievements.description','memberachievements.score','shanyraqs.name as shanyraq_name')
                                ->get()->toArray();
        return $memberachievement;
    }
    public function getListByShanyraqId($id){
        $memberachievements = $this->join('users','users.id','=','memberachievements.student_id')
                                ->join('studentgrades','studentgrades.id','=','memberachievements.studentgrade_id')
                                ->join('shanyraqs','shanyraqs.id','=','memberachievements.shanyraq_id')
                                ->join('places','places.id','=','memberachievements.place_id')
                                ->join('subcategories','subcategories.id','=','places.subcategory_id')
                                ->join('categories','categories.id','=','subcategories.category_id')
                                ->where('shanyraq.id',$id)
                                ->select('memberachievements.id','users.name as student_name','places.name as place_name','subcategories.name as subcategory_name','categories.name as category_name','memberachievements.description','memberachievements.score','shanyraqs.name as shanyraq_name')
                                ->get()->toArray();
        return $memberachievements;
    }
    public function getList(){
        $memberachievements = $this->join('users','users.id','=','memberachievements.student_id')
                                ->join('studentgrades','studentgrades.id','=','memberachievements.studentgrade_id')
                                ->join('shanyraqs','shanyraqs.id','=','memberachievements.shanyraq_id')
                                ->join('places','places.id','=','memberachievements.place_id')
                                ->join('subcategories','subcategories.id','=','places.subcategory_id')
                                ->join('categories','categories.id','=','subcategories.category_id')
                                ->select('memberachievements.id','users.name as student_name','places.name as place_name','subcategories.name as subcategory_name','categories.name as category_name','memberachievements.description','memberachievements.score','shanyraqs.name as shanyraq_name')
                                ->get()->toArray();
        return $memberachievements;
    }
    public function getUserList($token){
        $memberachievements = $this->join('users','users.id','=','memberachievements.student_id')
                                ->join('studentgrades','studentgrades.id','=','memberachievements.studentgrade_id')
                                ->join('shanyraqs','shanyraqs.id','=','memberachievements.shanyraq_id')
                                ->join('places','places.id','=','memberachievements.place_id')
                                ->join('subcategories','subcategories.id','=','places.subcategory_id')
                                ->join('categories','categories.id','=','subcategories.category_id')
                                ->where('users.token',$token)
                                ->select('memberachievements.id','users.name as student_name','places.name as place_name','subcategories.name as subcategory_name','categories.name as category_name','memberachievements.description','memberachievements.score','shanyraqs.name as shanyraq_name')
                                ->get()->toArray();
        return $memberachievements;
    }
    public function getListByUserId($id){
        $memberachievements = $this->join('users','users.id','=','memberachievements.student_id')
                                ->join('studentgrades','studentgrades.id','=','memberachievements.studentgrade_id')
                                ->join('shanyraqs','shanyraqs.id','=','memberachievements.shanyraq_id')
                                ->join('places','places.id','=','memberachievements.place_id')
                                ->join('subcategories','subcategories.id','=','places.subcategory_id')
                                ->join('categories','categories.id','=','subcategories.category_id')
                                ->where('users.id',$id)
                                ->select('memberachievements.id','users.name as student_name','places.name as place_name','subcategories.name as subcategory_name','categories.name as category_name','memberachievements.description','memberachievements.score','shanyraqs.name as shanyraq_name')
                                ->get()->toArray();
        return $memberachievements;
    }

}
