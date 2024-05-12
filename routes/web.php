<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\MailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//register page hit url
Route::get('/register',function(){
  return view('register');
});

//login page hit url
Route::get('/login',function(){
  return view('login');
});

//profile page hit url
Route::get('/profile',function(){
  return view('profile');
});
Route::get('/verify-mail/{token}',[UserController::class,'verificationMail']);

//testing for me
Route::get('send-mail',[MailController::class,'index']);

// forgotpassword
Route::get('/reset-password',[UserController::class,'resetPasswordLoad']);
Route::post('/reset-password',[UserController::class,'resetPassword']);

//hit front page
Route::get('/forget-password',function(){
    return view('forget-password');
});



