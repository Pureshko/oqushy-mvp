<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    public function getCategories($type = null){
        if(!empty($type)){
            return $this->where('type','like',"%".$type."%")->select('*')->get()->toArray();
        }
        return $this->select('*')->get()->toArray();
    }
    public function getSubcategories($categoryId,$type = null){
        if(!empty($type)){
            return $this->join('subcategories','subcategories.category_id','=','categories.id')
                        ->where('subcategories.category_id','=',$categoryId)
                        ->where('subcategories.type','like',"%".$type."%")
                        ->select('subcategories.id','subcategories.name')->get()->toArray();
        }
        return $this->join('subcategories','subcategories.category_id','=','categories.id')
                    ->where('subcategories.category_id','=',$categoryId)
                    ->select('subcategories.id','subcategories.name')->get()->toArray();
    }
    public function getPlaces($placeId){
        return $this->join('subcategories','subcategories.category_id','=','categories.id')
                    ->join('places','places.subcategory_id','=','subcategories.id')
                    ->where('places.subcategory_id','=',$placeId)
                    ->select('places.id','places.place')->get()->toArray();
    }
}
