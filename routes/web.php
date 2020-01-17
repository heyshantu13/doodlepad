<?php

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

use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMailable;



Route::get('sendmail',function(){
Mail::to('heyshantu13@gmail.com')->send(new SendOtpMailable());
});


Route::get('find',function(){

$data = App\User::where('mobile','9765679147');

	
});


Route::get('/', function () {
    return view('welcome');
});




Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

