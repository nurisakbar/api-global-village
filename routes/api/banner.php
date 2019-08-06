<?php
/*
|--------------------------------------------------------------------------
| Web Routes for user type
|--------------------------------------------------------------------------
|
 */
// 
Route::get('/','BannerController@index');
Route::post('/','BannerController@store');
Route::delete('/{id}','BannerController@destroy');

