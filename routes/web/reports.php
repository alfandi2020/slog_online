<?php

/**
 * Reports Routes
 */
Route::group([
    'as'         => 'reports.',
    'prefix'     => 'reports',
    'namespace'  => 'Reports',
    'middleware' => ['role:1|2|9'],
], function () {
    /**
     * Omzet Report Routes
     */
    Route::group(['prefix' => 'omzet', 'as' => 'omzet.'], function () {
        Route::get('daily', ['as' => 'daily', 'uses' => 'OmzetController@daily']);
        Route::get('monthly', ['as' => 'monthly', 'uses' => 'OmzetController@monthly']);
        Route::get('yearly', ['as' => 'yearly', 'uses' => 'OmzetController@yearly']);
    });

    Route::group(['prefix' => 'omzet-recap', 'as' => 'omzet-recap.'], function () {
        Route::get('monthly', ['as' => 'monthly', 'uses' => 'OmzetRecapController@monthly']);
        Route::get('yearly', ['as' => 'yearly', 'uses' => 'OmzetRecapController@yearly']);
    });

    Route::group(['prefix' => 'comodity-omzet', 'as' => 'comodity-omzet.'], function () {
        Route::get('monthly', ['as' => 'monthly', 'uses' => 'ComodityOmzetController@monthly']);
        Route::get('yearly', ['as' => 'yearly', 'uses' => 'ComodityOmzetController@yearly']);
    });

    Route::group(['prefix' => 'network-omzet', 'as' => 'network-omzet.'], function () {
        Route::get('daily', ['as' => 'daily', 'uses' => 'NetworkOmzetController@daily']);
        Route::get('monthly', ['as' => 'monthly', 'uses' => 'NetworkOmzetController@monthly']);
        Route::get('yearly', ['as' => 'yearly', 'uses' => 'NetworkOmzetController@yearly']);
    });

    /**
     * Time Series Report Routes
     */
    Route::group(['prefix' => 'time_series', 'as' => 'time_series.'], function () {
        Route::get('omzet', ['as' => 'omzet', 'uses' => 'TimeSeriesController@omzet']);
        Route::get('invoice', ['as' => 'invoice', 'uses' => 'TimeSeriesController@invoice']);
        Route::get('closed_invoice', ['as' => 'closed_invoice', 'uses' => 'TimeSeriesController@closedInvoice']);
    });
});

/**
 * Receipt Report Routes
 */
Route::group([
    'as'         => 'reports.receipt.',
    'prefix'     => 'reports/receipt',
    'namespace'  => 'Reports',
    'middleware' => ['role:1|2|3|4|5|6|9'],
], function () {
    Route::get('export', ['as' => 'export', 'uses' => 'ReceiptController@export']);
    Route::get('unreturned', ['as' => 'unreturned', 'uses' => 'ReceiptController@unreturned']);
    Route::get('returned', ['as' => 'returned', 'uses' => 'ReceiptController@returned']);
    Route::get('retained', ['as' => 'retained', 'uses' => 'ReceiptController@retained']);
    Route::get('late', ['as' => 'late', 'uses' => 'ReceiptController@late']);
});

/**
 * Invoice Report Routes
 */
Route::group([
    'as'         => 'reports.',
    'prefix'     => 'reports/invoices',
    'namespace'  => 'Reports',
    'middleware' => ['role:1|2|9'],
], function () {
    Route::get('/', ['as' => 'invoices', 'uses' => 'InvoiceController@index']);
    Route::get('/account-receivables', ['as' => 'invoices.receivables', 'uses' => 'InvoiceController@accountReceivables']);
});

/**
 * Receipt Report Routes
 */
Route::group([
    'as'         => 'reports.manifests.',
    'prefix'     => 'reports/manifests',
    'namespace'  => 'Reports',
    'middleware' => ['role:1|4|5|9'],
], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'ManifestController@distributions']);
    Route::get('/distributions', ['as' => 'distributions', 'uses' => 'ManifestController@distributions']);
});
