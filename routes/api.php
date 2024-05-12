<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/forget-password',[UserController::class,'forgotPassword']);

Route::group(['middleware'=>'api'],function($routes){

   //register api url hit in postman (http://127.0.0.1:8000/api/register)
   Route::post('/register',[UserController::class,'register']);

   //login api url hit in postman (http://127.0.0.1:8000/api/login)
   Route::post('/login',[UserController::class,'login']);

   //logout
   Route::post('/logout',[UserController::class,'logout']);

   //profile
   Route::get('/profile',[UserController::class,'profile']);
   Route::post('/update-profile',[UserController::class,'updateProfile']);
   Route::get('/send-verify-mail/{email}',[UserController::class,'sendVerifyMail']);
   Route::get('/refresh-token',[UserController::class,'refreshToken']);


});
