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
        Route::get('/chats/conversations', 'Chat\UserChatsController@index');
        Route::get('/chats/{receivableType}/{receivable}', 'Chat\UserChatsController@withReceivable');
        Route::resource('/chats', 'Chat\ChatsController')->only('store');
        Route::get('/user', 'User\CurrentUserController@show');
    });
});
