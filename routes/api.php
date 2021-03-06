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

Route::group(['namespace' => 'App\Http\Controllers\Auth'], function() {

    Route::group(['middleware' => ['auth:api'/*, 'api_token:api'*/]], function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::post('logout', 'LoginController@logout');
    });

    Route::post('register', 'RegisterController@register');
    Route::post('login', 'LoginController@login');
    Route::post('refresh_token', 'LoginController@refresh_token');

});
