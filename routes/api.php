<?php

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

Route::namespace('Api')->group(function () {
    Route::post('/login', 'Auth\LoginController@login');
    Route::post('/refresh-token', 'Auth\LoginController@refreshToken');

    Route::middleware('auth:api')->group(function () {
        Route::resource('/chats', 'User\CurrentUserController')->only('show');
        Route::get('/user', 'User\CurrentUserController@show');
    });
});
