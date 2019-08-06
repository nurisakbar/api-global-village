<?php
/*
|--------------------------------------------------------------------------
| Web Routes for user type
|--------------------------------------------------------------------------
|
 */
Route::post('/createoffer','HarvestController@createOffer');
Route::post('/comment','HarvestController@createComment');
Route::get('/','HarvestController@index');
Route::get('/search','HarvestController@search');
Route::get('/related/{id}/{limit?}','HarvestController@related');

Route::get('/comment/{id}','CommentController@HarvestGetComments');
Route::post('/comment','CommentController@HarvestCreateComment');

Route::get('/{id}','HarvestController@show');

Route::post('/','HarvestController@store');
Route::delete('/{id}','HarvestController@delete');
Route::post('/{id}','HarvestController@update');
Route::delete('image/{id}/{field}','HarvestController@deletePhoto');
Route::get('getoffer/{user}/{token}','HarvestController@getOffer');
Route::get('/category/{id}/{start?}/{limit?}','HarvestController@category');
