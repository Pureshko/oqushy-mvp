<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\Losts;
use App\Models\User;
use Illuminate\Http\Request;

class LostController extends ApiController
{
    protected $insertValidatedArray = [
        'name'=>'required|max:255',
        'description'=>'required|max:10000',
        'contact'=>'required|max:255',
        'photo1'=>'nullable|image',
        'photo2'=>'nullable|image',
        'photo3'=>'nullable|image',
    ];
    protected $dataArray = [
        'name',
        'description',
        'contact',
        'user_id',
        'type'=>'LOST',
        'status'=>1
    ];
    protected $updateValidateArray=[
        'name'=>'required|max:255',
        'description'=>'required|max:10000',
        'contact'=>'required|max:255',
        'photo1'=>'nullable|image',
        'photo2'=>'nullable|image',
        'photo3'=>'nullable|image',
    ];
    protected $modelName='Losts';
    protected $modelIdName='lost_id';
    protected $path="losts/lost_";
    protected $defaultPath='losts/default.jpg';
    public function index(Request $request){
        $response = $this->BaseApiModel()->getFullList();
        if(empty($response)){
            return response()->json(['message'=>'No losts found'],404);
        }
        $result = [
            'status'=>(bool)$response,
            'response'=>$response
        ];
        return response()->json($result,200);
    }

    public function BaseApiModel(){
        return new Losts();
    }
    public function User(){
        return new User();
    }
}
