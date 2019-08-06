<?php
/*
|--------------------------------------------------------------------------
| Web Routes for user type
|--------------------------------------------------------------------------
|
 */

Route::post('additem','OrderController@addItem');
Route::post('checkout','OrderController@checkOut');
Route::put('updateqty','OrderController@updateQty');
Route::get('getItem/{token}/{orderId?}','OrderController@getItem');
Route::delete('item/{id}','OrderController@deleteItem');
Route::get('purchase/{token}','OrderController@purchase');
Route::get('purchasedetail/{id}','OrderController@purchaseDetail');
Route::get('selling/{token}','OrderController@selling');
Route::get('sellingdetail/{id}','OrderController@purchaseDetail');
Route::post('confirm','OrderController@confirm');
