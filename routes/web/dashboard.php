<?php

Route::group(['as' => 'dashboard.', 'middleware' => 'auth'], function () {
    Route::get('receipt-monitor-chart', ['as' => 'monitor.chart', 'uses' => 'DashboardController@chart']);
    Route::get('monitor/per_network', ['as' => 'monitor.per-network', 'uses' => 'DashboardController@perNetwork']);
    Route::get('monitor/uninvoiced', ['as' => 'monitor.uninvoiced', 'uses' => 'DashboardController@uninvoiced']);
    Route::get('monitor/invoiced', ['as' => 'monitor.invoiced', 'uses' => 'DashboardController@invoiced']);
    Route::get('monitor/paid', ['as' => 'monitor.paid', 'uses' => 'DashboardController@paid']);
});
