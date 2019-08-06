<?php
/*
|--------------------------------------------------------------------------
| Web Routes for Region type
|--------------------------------------------------------------------------
|
 */

// propinsi
Route::get('/province','RegionController@province');
// kabupaten
Route::get('/regency/{province_id}','RegionController@regency');
// kecamatan
Route::get('/district/{regency_id}','RegionController@district');
// list desa dalam sebuah kecamatan
Route::get('/sub-district/{district_id}','RegionController@sub-district');

Route::get('/villages/{district_id}','RegionController@villages');
Route::get('/village/{village_id}','RegionController@villageDetail');
