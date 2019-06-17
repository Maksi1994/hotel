<?php

use Illuminate\Http\Request;

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

Route::group([
    'prefix' => 'users'
], function() {
    Route::post('/regist', 'UsersController@regist');
    Route::post('/login', 'UsersController@login');
    Route::get('/logout', 'UsersController@logout')->middleware('auth:api');
    Route::get('/accept-registration/{token}', 'UsersController@acceptRegistration');
});

Route::group([
    'prefix' => 'backend',
    'namespace' => 'Backend',
    'middleware' => ['auth:api', 'access:admin']
], function() {

    Route::group([
        'prefix' => 'rooms',
    ], function () {
        Route::post('/save', 'RoomsController@save');
        Route::get('/get-busy-room-numbers', 'RoomsController@getBusyRoomNumbers');
        Route::get('/get-one/{id}', 'RoomsController@getOne');
        Route::post('/get-list', 'RoomsController@getList');
        Route::get('/remove/{id}', 'RoomsController@remove');
    });

    Route::group([
        'prefix' => 'guests',
    ], function() {
        Route::post('/save', 'GuestsController@save');
        Route::post('/get-list', 'GuestsController@getList');
        Route::get('/get-one/{id}', 'GuestsController@getOne');
        Route::get('/remove/{id}', 'GuestsController@remove');
    });

    Route::group([
        'prefix' => 'users',
    ], function() {
        Route::post('/update', 'UsersController@updateUser');
        Route::get('/get-user/{id}', 'UsersController@getUser');
        Route::post('/get-list', 'UsersController@getUsersList');
        Route::get('/delete/{id}', 'UsersController@deleteUser');
    });

    Route::group([
        'prefix' => 'roles',
    ], function() {
        Route::post('/save', 'UsersController@saveRole');
        Route::get('/get-role/{id}', 'UsersController@getRole');
        Route::post('/get-list', 'UsersController@getRolesList');
        Route::get('/delete/{id}', 'UsersController@deleteUser');
    });

    Route::group([
        'prefix' => 'services',
    ], function() {
        Route::post('/save', 'ServicesController@save');
        Route::get('/get-one/{id}', 'ServicesController@getOne');
        Route::post('/get-list', 'ServicesController@getList');
        Route::get('/delete/{id}', 'ServicesController@delete');
    });

});
