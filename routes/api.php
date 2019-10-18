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
        Route::namespace('Chat')->group(function () {
            Route::get('/chats/conversations', 'UserChatsController@index');
            Route::get('/chats/{receivableType}/{receivable}', 'UserChatsController@withReceivable');
            Route::resource('/chats', 'ChatsController')->only('store');
        });

        Route::namespace('User')->group(function () {
            Route::get('/user', 'CurrentUserController@show');
            Route::resource('/users', 'UsersController')->only('show');
            Route::post('/users/online', 'UsersController@storeOnlineStatus');
        });

        Route::namespace('Group')->group(function () {
            Route::resource('/groups', 'GroupsController')->only('show');
        });
    });
});
