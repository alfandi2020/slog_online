<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::view('/', 'auth.login')->middleware('guest');

require __DIR__ . '/web/auth.php';
require __DIR__ . '/web/admin.php';
require __DIR__ . '/web/dashboard.php';
require __DIR__ . '/web/accounting.php';
require __DIR__ . '/web/receipts.php';
require __DIR__ . '/web/manifests.php';
require __DIR__ . '/web/reports.php';
require __DIR__ . '/web/invoices.php';
require __DIR__ . '/web/services.php';
require __DIR__ . '/web/sales-counter.php';

Route::group(['middleware' => 'auth'], function () {
    Route::get('home', ['as' => 'home', 'uses' => 'PagesController@index']);
    Route::get('get-costs', ['as' => 'pages.get-costs', 'uses' => 'PagesController@getCosts']);
    Route::post('get-costs', ['as' => 'pages.get-costs', 'uses' => 'PagesController@getCosts']);
    Route::get('receipts/retained', ['as' => 'receipts.retained', 'uses' => 'Reports\ReceiptController@retained']);
    Route::get('receipts', ['as' => 'receipts.index', 'uses' => 'Receipts\ReceiptsController@index']);
    Route::get('receipts/{receipt}', ['as' => 'receipts.show', 'uses' => 'Receipts\ReceiptsController@show']);
    Route::get('notifications', ['as' => 'pages.notifications', 'uses' => 'PagesController@notifications']);
});

Route::get('barcode/img/{text}/{size?}/{scale?}', 'Services\BarcodeController@show');

Route::get('phpmyinfo', function () {
    phpinfo();
})->name('phpmyinfo');
