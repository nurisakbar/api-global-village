<?php
/*
|--------------------------------------------------------------------------
| Web Routes for user type
|--------------------------------------------------------------------------
|
 */

Route::post('/register','UserController@register');
Route::post('/login','UserController@login');

Route::put('/update-profile','UserController@updateProfile');
Route::post('/update-profile-image','UserController@updateProfileImage');