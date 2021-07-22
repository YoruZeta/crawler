<?php

use Illuminate\Support\Facades\Route;

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

//Route::get('/', 'WebServiceController@send');
//Route::get('test', [WSController::class, 'send']);
Route::get('/index', 'App\Http\Controllers\WSController@index')->name('index'); ;
Route::get('/home', 'App\Http\Controllers\WSController@home')->name('home'); ;

// Route::get('/', function () {
//     return view('welcome');
// });


