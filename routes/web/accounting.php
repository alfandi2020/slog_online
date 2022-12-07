<?php

Route::group(['prefix' => 'accounting', 'middleware' => 'role:1|2|9'], function () {
    /**
     * Rate Exports Routes
     */
    Route::get('rates/exports', ['as' => 'rates.exports.index', 'uses' => 'Services\RateExportsController@index']);
    Route::get('rates/exports/excel', ['as' => 'rates.exports.excel', 'uses' => 'Services\RateExportsController@excel']);
    Route::get('rates/exports/base', ['as' => 'rates.exports.base', 'uses' => 'Services\RateExportsController@base']);
    Route::get('rates/exports/customer', ['as' => 'rates.exports.customer', 'uses' => 'Services\RateExportsController@customer']);

    /**
     * Base Rates Routes
     */
    Route::get('rates/list', ['as' => 'rates.list', 'uses' => 'Services\RatesController@list']);
    Route::post('rates/list-store', ['as' => 'rates.list-store', 'uses' => 'Services\RatesController@listStore']);
    Route::get('rates/{rate}/delete', ['as' => 'rates.delete', 'uses' => 'Services\RatesController@delete']);
    Route::resource('rates', 'Services\RatesController');

    /**
     * Customers Routes
     */
    Route::get('customers/{customer}/un-invoiced-receipts', [
        'as'   => 'customers.un-invoiced-receipts',
        'uses' => 'Customers\CustomersController@unInvoicedReceipts',
    ]);
    Route::get('customers/{customer}/invoiced-receipts', [
        'as'   => 'customers.invoiced-receipts',
        'uses' => 'Customers\CustomersController@invoicedReceipts',
    ]);
    Route::get('customers/{customer}/invoices', ['as' => 'customers.invoices', 'uses' => 'Customers\CustomersController@invoices']);
    Route::get('customers/{customer}/delete', ['as' => 'customers.delete', 'uses' => 'Customers\CustomersController@delete']);
    Route::get('customers/export', ['as' => 'customers.export', 'uses' => 'Customers\CustomersController@export']);
    Route::resource('customers', 'Customers\CustomersController');

    /**
     * Customer Rates Routes
     */
    Route::resource('customers.rates', 'Customers\RatesController');

    /**
     * Payment methods Routes
     */
    Route::apiResource('payment-methods', 'Transactions\PaymentMethodsController');
});
