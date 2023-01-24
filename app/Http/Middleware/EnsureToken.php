<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class EnsureToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = new User();
        $response = $user->where('apiKey',$request->header('Authorization'))->get()->toArray();
        if(sizeof($response)==1){
            return $next($request);
        }else{
            return response()->json(["success"=>false,'message'=>'Invalid token'],401);
        }
        
    }
}
