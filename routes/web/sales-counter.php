<?php

Route::group(['prefix' => 'sales-counter', 'as' => 'reports.sales-counter.', 'middleware' => 'role:3'], function() {
    /**
     * Sales Counter Daily Report Routes
     */
    Route::get('daily', ['as' => 'daily', 'uses' => 'Reports\SalesCountersController@daily']);
});

