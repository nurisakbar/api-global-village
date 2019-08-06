<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\ProductComment;
use App\OrderItem;
use App\Order;
use App\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ab','OrderController@createInvoiceNumber');


Route::get('/abc',function()
{
//    $order = Order::find('2be0acf5-e755-4194-9901-47ecc14810cf');
//    return $order->PurchaseItem;  
    //$user = User::find(1);
    //return $user->full_address; 
});