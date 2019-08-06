<?php
/*
|--------------------------------------------------------------------------
| Web Routes for user type
|--------------------------------------------------------------------------
|
 */
Route::post('/','LandController@store');
Route::delete('/{id}','LandController@destroy');
Route::get('/{id}','LandController@show');
Route::post('/{id}','LandController@update');