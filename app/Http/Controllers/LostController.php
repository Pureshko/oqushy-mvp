<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Losts;
use App\Models\User;
use \Illuminate\Support\Facades\Validator;

class LostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $losts = $this->Losts()->getLostList();
        return response()->json($losts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        $credentials = Validator::make($inputs, [
            'name'=>'required|max:255',
            'description'=>'required|max:10000',
            'contact'=>'required|max:255',
            'photo1'=>'nullable|image',
            'photo2'=>'nullable|image',
            'photo3'=>'nullable|image',
        ]);
        if($credentials->fails()){
            return response()->json([
                'status'=>false,
                'body'=>$credentials->errors()
            ], 400);
        }
        $data = [
            'name'=>$credentials['name'],
            'description'=>$credentials['description'],
            'contact'=>$credentials['contact'],
            'user_id'=>$this->User()->getUserId($request->header('Authorization')),
            'status'=>'LOST',
        ];
        $lostId = $this->Losts()->createLost($data);
        if(isset($lostId)){
            $photo1 = $request->hasFile('photo1') ? $request->file('photo1') : NULL;
            $photo2 = $request->hasFile('photo2') ? $request->file('photo2') : NULL;
            $photo3 = $request->hasFile('photo3') ? $request->file('photo3') : NULL;
            $photo1Name = $request->hasFile('photo1') ? $lostId.'-1.'.$photo1->extension() : NULL;
            $photo2Name = $request->hasFile('photo2') ? $lostId.'-2.'.$photo2->extension() : NULL;
            $photo3Name = $request->hasFile('photo3') ? $lostId.'-3.'.$photo3->extension() : NULL;
            if(!$photo1 && !$photo2 && !$photo3){
                $photo1Name = 'losts/default.jpg';
            }
            mkdir(storage_path('app/public/losts/lost_'.str($lostId)));
            if($photo1){
                $photo1->move(storage_path('app/public/losts/lost_'.str($lostId)), $photo1Name);
                $photo1Name = 'losts/lost_'.str($lostId).'/'.$photo1Name;
            }
            if($photo2){
                $photo2->move(storage_path('app/public/losts/lost_'.str($lostId)), $photo2Name);
                $photo2Name = 'losts/lost_'.str($lostId).'/'.$photo2Name;
            }
            if($photo3){
                $photo3->move(storage_path('app/public/losts/lost_'.str($lostId)), $photo3Name);
                $photo3Name = 'losts/lost_'.str($lostId).'/'.$photo3Name;
            }
            $succ = $this->Losts()->createLostFiles($lostId, [
                'photo1'=>$photo1Name,
                'photo2'=>$photo2Name,
                'photo3'=>$photo3Name,
            ]);
            if(!$succ){
                return response()->json([
                    'status'=>'error',
                    'message'=>'Lost created successfully, but photos not uploaded',
                    'body'=>[
                        'lost_id'=>$lostId,
                    ],
                ]);
            }
            return response()->json([
                'status'=>'success',
                'message'=>'Lost created successfully',
                'body'=>[
                    'lost_id'=>$lostId,
                ],
            ]);
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>'Lost not created',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lost = $this->Losts()->getLost($id);
        if($lost){
            return response()->json([
                'status'=>'success',
                'message'=>'Lost found',
                'body'=>$lost,
            ]);
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>'Lost not found',
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $credentials = [
            'name'=>$request->input('name'),
            'description'=>$request->input('description'),
            'contact'=>$request->input('contact'),
        ];
        $data = [
            'name'=>$credentials['name'],
            'description'=>$credentials['description'],
            'contact'=>$credentials['contact'],
            'user_id'=>$this->User()->getUserId($request->header('Authorization')),
            'status'=>'FND',
        ];
        $result = $this->Losts()->updateLost($id, $data);
        if($result){
            return response()->json([
                'status'=>'success',
                'message'=>'Lost updated successfully',
            ]);
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>'Lost not updated',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->Losts()->destroyLost($id);
        if($result){
            return response()->json([
                'status'=>'success',
                'message'=>'Lost deleted successfully',
            ]);
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>'Lost not deleted',
            ]);
        }
    }

    public function Losts(){
        return new Losts();
    }
    public function User(){
        return new User();
    }
}
