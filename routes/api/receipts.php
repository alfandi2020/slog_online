<?php

Route::post('receipts', ['as' => 'receipts.index', 'uses' => 'ReceiptsController@index']);
Route::get('receipts/{receipt}', ['as' => 'receipts.show', 'uses' => 'ReceiptsController@show']);