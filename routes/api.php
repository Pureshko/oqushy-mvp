<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LostController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('ensure.token')->group(function(){
    Route::apiResource('/achievements', AchievementController::class);
    Route::apiResource('/losts', LostController::class);
    Route::prefix('/scores')->group(function(){
        Route::get('/userlist',[ScoreController::class, 'getUserRankedList']);
        Route::get('/gradelist',[ScoreController::class, 'getGradesRankedList']);
        Route::get('/shanyraqlist',[ScoreController::class, 'getShanyraqRankedList']);
    });
    Route::prefix('/categories')->group(function(){
        Route::get('/',[CategoryController::class,'getCategories']);
        Route::get('/{id}',[CategoryController::class,'getSubcategories']);
        Route::get('/places/{id}',[CategoryController::class,'getPlaces']);
    });
    Route::prefix('/moderation')->group(function(){
        Route::get('/',[ModerationController::class,'getNonModeratedAchievements']);
        Route::post('/{id}',[ModerationController::class,'acceptOrDeclineAchievement']);
    }); 
    Route::post('/user', [UserController::class,"getUser"]);
});
Route::post('/auth', [LoginController::class,"authenticate"]);
