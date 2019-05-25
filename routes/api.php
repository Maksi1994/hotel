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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'prefix' => 'backend',
    'namespace' => 'Backend'
], function() {
    Route::group([
        'prefix' => 'rooms',
    ], function () {
        Route::post('/save', 'RoomsController@save');
        Route::get('/get-busy-room-numbers', 'RoomsController@getBusyRoomNumbers');
        Route::get('/get-one/{id}', 'RoomsController@save');
        Route::post('/get-list', 'RoomsController@getList');
        Route::get('/remove/{id}', 'RoomsController@remove');
    });


    Route::group([
        'prefix' => 'guests',
    ], function() {
        Route::post('/save', 'GuestsController@save');
    });

});








