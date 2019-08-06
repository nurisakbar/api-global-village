<?php
/*
|--------------------------------------------------------------------------
| Web Routes for user type
|--------------------------------------------------------------------------
|
 */

// menambahkan komentar pada video
Route::post('/comment','CommentController@VideoCreateComment');

// menampilkan data komentar berdasarkan video id
Route::get('/comment/{id}/{start?}/{limit?}','CommentController@VideoGetComments');

// menampilkan semua data video
Route::get('/','VideoController@index');

// menampilkan data video berdasarkan keywword pencarian
Route::get('/search','VideoController@search');

// menampilkan video yang berhubungan berdasarkan judul
Route::get('/related/{id}/{limit?}','VideoController@related');

// menampilkan data detail video
Route::get('/{id}','VideoController@show');

// menampilkan data video berdasarkan kategori
Route::get('/category/{id}/{start?}/{limit?}','VideoController@category');


Route::post('/','VideoController@store');
Route::delete('/{id}','VideoController@delete');
Route::post('/{id}','VideoController@update');
