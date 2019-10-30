<?php

use Illuminate\Http\Request;
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
#Register/Login routes
Route::post('/register', 'Auth\AuthController@register');
Route::post('/login', 'Auth\AuthController@login');

#Auth routes
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/', function () {
        return 'Success';
    });
    Route::group(['prefix' => 'game'], function () {
        Route::get('tracks', 'GameController@index');
        Route::post('guess', 'GuessController@store');
    });

});
