<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;

class CategoryController extends Controller
{
    public function getCategories(Request $request){
        $type = $request->validate(['type'=>'optional']);
        if(!empty($type)){
            return response()->json($this->Categories()->getCategories($type));
        }
        return response()->json($this->Categories()->getCategories());
    }
    public function getSubcategories(Request $request,$id){
        $type = $request->validate(['type'=>'optional']);
        if(!empty($type)){
            return response()->json($this->Categories()->getSubcategories($id,$type));
        }
        return response()->json($this->Categories()->getSubcategories($id));
    }
    public function getPlaces($id){
        return response()->json($this->Categories()->getPlaces($id));    
    }
    public function Categories(){
        return new Categories();
    }
}
