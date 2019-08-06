<?php
/*
|--------------------------------------------------------------------------
| Web Routes for user type
|--------------------------------------------------------------------------
|
 */

Route::get('/','CategoryController@index');
Route::get('/pluck/{entity?}','CategoryController@pluck');
Route::get('/{id}','CategoryController@show');
Route::post('/','CategoryController@store');
Route::delete('/{id}','CategoryController@destroy');
Route::post('/{id}','CategoryController@update');
