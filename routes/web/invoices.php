<?php

Route::group(['namespace' => 'Invoices', 'prefix' => 'accounting', 'middleware' => 'role:1|2|6|9'], function () {
    /**
     * Credit Invoice Routes
     */
    Route::get('invoices/search', ['as' => 'invoices.search', 'uses' => 'InvoicesController@search']);
    Route::get('invoices/customer-list', ['as' => 'invoices.customer-list', 'uses' => 'InvoicesController@customerList']);
    Route::get('invoices/create/{customer}', ['as' => 'invoices.create', 'uses' => 'InvoicesController@create']);
    Route::post('invoices/create/{customer}', ['as' => 'invoices.store', 'uses' => 'InvoicesController@store']);
    Route::get('invoices/{invoice}', ['as' => 'invoices.show', 'uses' => 'InvoicesController@show']);
    Route::get('invoices/{invoice}/edit', ['as' => 'invoices.edit', 'uses' => 'InvoicesController@edit']);
    Route::get('invoices/{invoice}/pdf', ['as' => 'invoices.pdf', 'uses' => 'InvoicesController@pdf']);
    Route::get('invoices/{invoice}/export-xls', ['as' => 'invoices.export-xls', 'uses' => 'InvoicesController@exportXls']);
    Route::patch('invoices/{invoice}/update', ['as' => 'invoices.update', 'uses' => 'InvoicesController@update']);
    Route::get('invoices/{invoice}/delete', ['as' => 'invoices.delete', 'uses' => 'InvoicesController@delete']);
    Route::get('invoices', ['as' => 'invoices.index', 'uses' => 'InvoicesController@index']);

    /**
     * Invoice Payments Routes
     */
    Route::post('invoices/{invoice}/payments', ['as' => 'invoices.payments.store', 'uses' => 'InvoicePaymentsController@store']);
    Route::patch('invoices/{invoice}/payments/{transaction}', ['as' => 'invoices.payments.update', 'uses' => 'InvoicePaymentsController@update']);

    /**
     * Invoice Receipts Routes
     */
    Route::get('invoices/{invoice}/receipts', ['as' => 'invoices.receipts.index', 'uses' => 'CreditReceiptsController@index']);

    /**
     * Invoice Delivery Routes
     */
    Route::patch('invoices/{invoice}/deliver', ['as' => 'invoices.deliver', 'uses' => 'InvoicesController@deliver']);
    Route::patch('invoices/{invoice}/store-delivery-info', ['as' => 'invoices.store-delivery-info', 'uses' => 'InvoicesController@updateDeliveryInfo']);

    Route::post('invoices/{invoice}/assign-receipt', ['as' => 'invoices.assign-receipt', 'uses' => 'InvoicesController@assignReceipt']);
    Route::post('invoices/{invoice}/remove-receipt', ['as' => 'invoices.remove-receipt', 'uses' => 'InvoicesController@removeReceipt']);
    Route::patch('invoices/{invoice}/deliver', ['as' => 'invoices.deliver', 'uses' => 'InvoicesController@deliver']);
    Route::patch('invoices/{invoice}/undeliver', ['as' => 'invoices.undeliver', 'uses' => 'InvoicesController@undeliver']);
    Route::patch('invoices/{invoice}/set-paid', ['as' => 'invoices.set-paid', 'uses' => 'InvoicesController@setPaid']);
    Route::patch('invoices/{invoice}/set-unpaid', ['as' => 'invoices.set-unpaid', 'uses' => 'InvoicesController@setUnpaid']);
    Route::patch('invoices/{invoice}/set-problem', ['as' => 'invoices.set-problem', 'uses' => 'InvoicesController@setProblem']);
    Route::delete('invoices/{invoice}/unset-problem', ['as' => 'invoices.unset-problem', 'uses' => 'InvoicesController@unsetProblem']);
    Route::patch('invoices/{invoice}/verify', ['as' => 'invoices.verify', 'uses' => 'InvoicesController@verify']);
    Route::delete('invoices/{invoice}', ['as' => 'invoices.destroy', 'uses' => 'InvoicesController@destroy']);
    Route::patch('invoices/receipt-cost-update/{receipt}', ['as' => 'invoices.receipt-cost-update', 'uses' => 'InvoicesController@receiptCostUpdate']);
});

Route::group(['namespace' => 'Invoices', 'middleware' => 'role:1|2|3|6|9'], function () {
    /**
     * Cash Invoice Routes
     */
    Route::post('cash-invoices/{invoice}/assign-receipt', ['as' => 'invoices.cash.assign-receipt', 'uses' => 'CashInvoicesController@assignReceipt']);
    Route::post('cash-invoices/{invoice}/remove-receipt', ['as' => 'invoices.cash.remove-receipt', 'uses' => 'CashInvoicesController@removeReceipt']);
    Route::get('cash-invoices/create', ['as' => 'invoices.cash.create', 'uses' => 'CashInvoicesController@create']);
    Route::post('cash-invoices/create', ['as' => 'invoices.cash.store', 'uses' => 'CashInvoicesController@store']);
    Route::patch('cash-invoices/{invoice}/deliver', ['as' => 'invoices.cash.deliver', 'uses' => 'CashInvoicesController@deliver']);
    Route::patch('cash-invoices/{invoice}/undeliver', ['as' => 'invoices.cash.undeliver', 'uses' => 'CashInvoicesController@undeliver']);
    Route::get('cash-invoices/{invoice}', ['as' => 'invoices.cash.show', 'uses' => 'CashInvoicesController@show']);
    Route::get('cash-invoices/{invoice}/edit', ['as' => 'invoices.cash.edit', 'uses' => 'CashInvoicesController@edit']);
    Route::get('cash-invoices/{invoice}/pdf', ['as' => 'invoices.cash.pdf', 'uses' => 'CashInvoicesController@pdf']);
    Route::patch('cash-invoices/{invoice}/update', ['as' => 'invoices.cash.update', 'uses' => 'CashInvoicesController@update']);
    Route::delete('cash-invoices/{invoice}', ['as' => 'invoices.cash.destroy', 'uses' => 'CashInvoicesController@destroy']);
    Route::patch('cash-invoices/{invoice}/verify', ['as' => 'invoices.cash.verify', 'uses' => 'CashInvoicesController@verify']);
    Route::get('cash-invoices', ['as' => 'invoices.cash.index', 'uses' => 'CashInvoicesController@index']);


    // Route::get('cash-invoices/{invoice}/payments', ['as' => 'invoices.cash.payments.index', 'uses' => 'CashPaymentsController@index']);
});

Route::group(['namespace' => 'Invoices', 'middleware' => 'role:1|2|5|6|9'], function () {
    /**
     * Cod Invoice Routes
     */
    Route::post('cod-invoices/{invoice}/assign-receipt', ['as' => 'invoices.cod.assign-receipt', 'uses' => 'CodInvoicesController@assignReceipt']);
    Route::post('cod-invoices/{invoice}/remove-receipt', ['as' => 'invoices.cod.remove-receipt', 'uses' => 'CodInvoicesController@removeReceipt']);
    Route::get('cod-invoices/create', ['as' => 'invoices.cod.create', 'uses' => 'CodInvoicesController@create']);
    Route::post('cod-invoices/create', ['as' => 'invoices.cod.store', 'uses' => 'CodInvoicesController@store']);
    Route::patch('cod-invoices/{invoice}/deliver', ['as' => 'invoices.cod.deliver', 'uses' => 'CodInvoicesController@deliver']);
    Route::patch('cod-invoices/{invoice}/undeliver', ['as' => 'invoices.cod.undeliver', 'uses' => 'CodInvoicesController@undeliver']);
    Route::get('cod-invoices/{invoice}', ['as' => 'invoices.cod.show', 'uses' => 'CodInvoicesController@show']);
    Route::get('cod-invoices/{invoice}/edit', ['as' => 'invoices.cod.edit', 'uses' => 'CodInvoicesController@edit']);
    Route::get('cod-invoices/{invoice}/pdf', ['as' => 'invoices.cod.pdf', 'uses' => 'CodInvoicesController@pdf']);
    Route::patch('cod-invoices/{invoice}/update', ['as' => 'invoices.cod.update', 'uses' => 'CodInvoicesController@update']);
    Route::delete('cod-invoices/{invoice}', ['as' => 'invoices.cod.destroy', 'uses' => 'CodInvoicesController@destroy']);
    Route::patch('cod-invoices/{invoice}/verify', ['as' => 'invoices.cod.verify', 'uses' => 'CodInvoicesController@verify']);
    Route::get('cod-invoices', ['as' => 'invoices.cod.index', 'uses' => 'CodInvoicesController@index']);

    /**
     * COD Invoice Payments Routes
     */
    Route::get('cod-invoices/{invoice}/payments', ['as' => 'invoices.cod.payments.index', 'uses' => 'CodPaymentsController@index']);
    Route::post('cod-invoices/{invoice}/payments', ['as' => 'invoices.cod.payments.store', 'uses' => 'CodPaymentsController@store']);
    Route::patch('cod-invoices/{invoice}/payments/{transaction}', ['as' => 'invoices.cod.payments.update', 'uses' => 'CodPaymentsController@update']);
});
