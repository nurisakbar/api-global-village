<?php
/*
|--------------------------------------------------------------------------
| Web Routes for user type
|--------------------------------------------------------------------------
|
 */
Route::get('/comment/{id}/{start?}/{limit?}','CommentController@ArticleGetComments');
Route::post('/comment','CommentController@ArticleCreateComment');


Route::get('/popular/{start?}/{limit?}/{idcategory?}','ArticleController@popular');
Route::get('/','ArticleController@index');
Route::get('/search','ArticleController@search');
Route::get('/related/{id}/{limit?}','ArticleController@related');
Route::get('/{id}','ArticleController@show');
Route::get('/category/{id}/{start?}/{limit?}','ArticleController@category');
Route::post('/','ArticleController@store');
Route::delete('/{id}','ArticleController@delete');
Route::post('/{id}','ArticleController@update');

