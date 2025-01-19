<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

Route::post('/signup', 'AuthController@signup');
Route::post('/login', 'AuthController@login');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/index', 'AuthController@index');
    Route::get('/show', 'AuthController@show');
    Route::post('/store', 'AuthController@store');
    Route::put('/update', 'AuthController@update');
    Route::delete('/destroy', 'AuthController@destroy');
    Route::post('/neww', 'AuthController@new');
});

