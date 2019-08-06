<?php
/*
|--------------------------------------------------------------------------
| Web Routes for user type
|--------------------------------------------------------------------------
|
 */

 
Route::post('/comment','CommentController@ProductCreateComment');
Route::get('/comment/{id}','CommentController@ProductGetComments');

Route::get('/','ProductController@index');
Route::get('/region/{regencyId}','ProductController@byRegion');

Route::get('/search','ProductController@search');
Route::get('/related/{id}/{limit?}','ProductController@related');
Route::get('/{id}','ProductController@show');
Route::get('/category/{id}/{start?}/{limit?}','ProductController@category');

Route::post('/','ProductController@store');
Route::post('/{id}','ProductController@update');
Route::delete('/{id}','ProductController@delete');
Route::put('/{id}','ProductController@update');
Route::get('getcomment/{id}/{limit?}','ProductController@getComment');

