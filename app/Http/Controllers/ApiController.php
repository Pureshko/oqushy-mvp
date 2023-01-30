<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaseApiModel;
use \Illuminate\Support\Facades\Validator;
use App\Models\User;

interface IApiController
{
    public function index(Request $request);
    public function store(Request $request);
    public function show($id);
    public function update(Request $request, $id);
    public function destroy($id);
    public function BaseApiModel();
}
abstract class ApiController extends Controller implements IApiController
{
    protected $insertValidateArray;
    protected $updateValidateArray;
    protected $dataArray;
    protected $modelName;
    protected $modelIdName;
    protected $path;
    protected $defaultPath;
    public function index(Request $request){
        $model = $this->BaseApiModel();
        $token = $request->header('Authorization');
        $response = $model->getFullList($token);
        if(empty($response)){
            $result = [
                'status'=>false,
                'message'=>'No '.strtolower($this->modelName).' found'
            ];
            return response()->json($result,404);
        }
        $result = [
            'status' => (bool)$response,
            'response'=> $response
        ];
        return response()->json($result);
    }
    protected function bindSupervisor($inputs,$modelId){}
    public function store(Request $request){
        $token = $request->header('Authorization');
        $inputs = $request->all();
        $validator = Validator::make($inputs,$this->insertValidateArray);
        if($validator->fails()){
            $result = [
                'status'=>false,
                'message'=>$validator->errors()
            ];
            return response()->json($result,400);
        }
        $data = [];
        foreach($this->dataArray as $key=>$value){
            if(isset($inputs[$value])){
                $data[$value] = $inputs[$value];
            }else{
                if($value == 'owner'){
                    $data[$value] = $this->User()->getUserId($token);
                }elseif($value == 'shanyraq_id'){
                    $data[$value] = $this->User()->getShanyraqId($token);
                }elseif($value == 'user_id'){
                    $data[$value] = $this->User()->getUserId($token);
                }elseif(isset($inputs[$key])){
                    if($key == 'rating'){
                        $data[$key] = $inputs[$key] == '0' ? 0 : 1 ;
                    }
                }else{
                    $data[$key] = $value;
                }
            }
        }
        $modelId = $this->BaseApiModel()->create($data,$token);
        if(isset($modelId)){
            $this->bindSupervisor($inputs,$modelId);
            if(empty($inputs['photo1']) && empty($inputs['photo2']) && empty($inputs['photo3'])){
                if(!empty($modelId)){
                    return response()->json([
                        'status'=>'error',
                        'message'=>substr($this->modelName, 0, -1).' created successfully, but photos not uploaded',
                        'body'=>[
                            $this->modelIdName=>$modelId,
                        ],
                    ]);
                }
                return response()->json([
                    'status'=>'success',
                    'message'=>substr($this->modelName, 0, -1).' created successfully',
                    'body'=>[
                        $this->modelIdName=>$modelId,
                    ],
                ]);
            }
            $photo1 = $request->hasFile('photo1') ? $request->file('photo1') : NULL;
            $photo2 = $request->hasFile('photo2') ? $request->file('photo2') : NULL;
            $photo3 = $request->hasFile('photo3') ? $request->file('photo3') : NULL;
            $photo1Name = $request->hasFile('photo1') ? $modelId.'-1.'.$photo1->extension() : NULL;
            $photo2Name = $request->hasFile('photo2') ? $modelId.'-2.'.$photo2->extension() : NULL;
            $photo3Name = $request->hasFile('photo3') ? $modelId.'-3.'.$photo3->extension() : NULL;
            if(!$photo1 && !$photo2 && !$photo3){
                $photo1Name = $this->defaultPath;
            }
            mkdir(storage_path('app/public/'.$this->path.str($modelId)));
            if($photo1){
                $photo1->move(storage_path('app/public/'.$this->path.str($modelId)), $photo1Name);
                $photo1Name = $this->path.str($modelId).'/'.$photo1Name;
            }
            if($photo2){
                $photo2->move(storage_path('app/public/'.$this->path.str($modelId)), $photo2Name);
                $photo2Name = $this->path.str($modelId).'/'.$photo2Name;
            }
            if($photo3){
                $photo3->move(storage_path('app/public/'.$this->path.str($modelId)), $photo3Name);
                $photo3Name = $this->path.str($modelId).'/'.$photo3Name;
            }
            $succ = $this->BaseApiModel()->createFiles($modelId, [
                'photo1'=>$photo1Name,
                'photo2'=>$photo2Name,
                'photo3'=>$photo3Name,
            ]);
            if(!$succ){
                return response()->json([
                    'status'=>'error',
                    'message'=>substr($this->modelName, 0, -1).' created successfully, but photos not uploaded',
                    'body'=>[
                        $this->modelIdName=>$modelId,
                    ],
                ]);
            }
            return response()->json([
                'status'=>'success',
                'message'=>substr($this->modelName, 0, -1).' created successfully',
                'body'=>[
                    $this->modelIdName=>$modelId,
                ],
            ]);
        }else{
            $result = [
                'status'=>false,
                'message'=>'Failed to create '.strtolower(substr($this->modelName, 0, -1))
            ];
            return response()->json($result,400);
        }
    }
    public function show($id){
        $model = $this->BaseApiModel();
        $response = $model->getById($id);
        if(empty($response)){
            $result = [
                'status'=>false,
                'message'=>substr($this->modelName, 0, -1).' not found'
            ];
            return response()->json($result,404);
        }
        $result = [
            'status' => (bool)$response,
            'response'=> $response
        ];
        return response()->json($result);
    }
    public function update(Request $request, $id){
        return 0;
    }
    public function destroy($id){
        $model = $this->BaseApiModel();
        $files = $model->BaseApiModel()->BaseApiFiles()->where($this->modelIdName,$id)->select('id')->get()->toArray();
        foreach($files as $file){
            $model->BaseApiModel()->BaseApiFiles()->destroy($file['id']);
        }
        $response = $model->destroy($id);
        if(empty($response)){
            $result = [
                'status'=>false,
                'message'=>substr($this->modelName, 0, -1).' not found'
            ];
            return response()->json($result,404);
        }
        $result = [
            'status' => (bool)$response,
            'response'=> $response
        ];
        return response()->json($result);
    }
    public function User(){
        return new User();
    }
    public function BaseApiModel(){
        return new BaseApiModel();
    }
}
