<?php

//home
Route::get('/', 'HomeController@index')->name('welcome');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/user/info', function () {
    $user = Auth::user()->toArray();
    $user['role'] = ['admin'];
    $user['avatar'] = 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif';
    return $user;
});
Route::get('/test', function () {
    return auth()->user();
});
Auth::routes();
//司机信息
Route::resource('drivers', 'DriversController', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
Route::resource('goods_settings', 'GoodsSettingsController', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
Route::resource('sub_order_payments', 'SubOrderPaymentsController', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
Route::resource('order_goods_payments', 'OrderGoodsPaymentsController', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
//换瓶盖
Route::resource('exchange_bottles', 'ExchangeBottlesController', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
//下拉列表
Route::get('/stores', 'StoresController@list');
Route::get('/drivers_list', 'DriversController@list');
//
Route::get('/main_order_payments', 'MainOrderPaymentsController@index')->name('main_order_payments.index');
Route::post('/main_order_payments', 'MainOrderPaymentsController@store');
Route::get('/main_order_payments/jizhang', 'MainOrderPaymentsController@jizhang')->name('main_order_payments.jizhang');
Route::get('/main_order_payments/fanjizhang', 'MainOrderPaymentsController@fanjizhang')->name('main_order_payments.fanjizhang');
Route::get('/main_order_payments/re_calculate', 'MainOrderPaymentsController@reCalculateShippingFee')->name('main_order_payments.re_calculate');
Route::get('/main_order_payments/{pay_id}', 'MainOrderPaymentsController@show');

//单据导出
Route::group(['prefix' => 'export'], function () {
    Route::get('driver', 'DriversController@export');
    Route::get('main_order_payments', 'MainOrderPaymentsController@export');
});

//导入
Route::group(['prefix' => 'import'], function () {
    Route::post('driver', 'DriversController@import');
    Route::post('goods_settings', 'GoodsSettingsController@import');
});