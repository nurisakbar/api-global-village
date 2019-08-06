<?php
/*
|--------------------------------------------------------------------------
| Web Routes for user type
|--------------------------------------------------------------------------
|
 */
Route::get('/','UserController@index');
Route::post('/register','UserController@register');
Route::post('/login','UserController@login');
Route::get('/profile/{token}','UserController@profile');
Route::put('/update-profile','UserController@updateProfile');
Route::post('/update-profile-image','UserController@updateProfileImage');

Route::get('/product/{token}','UserController@products');
Route::get('/harvest/{token}','UserController@harvest');
Route::get('/land/{token}','UserController@land');

Route::post('forgetpassword','UserController@forgetPassword');
Route::post('changepassword','UserController@changePassword');
Route::post('/deliverydestination','UserController@storeDeliveryDestination');
Route::get('/deliverydestination/{token}','UserController@getDeliveryDestination');
Route::post('registerambasador','UserController@registerAmbasador');