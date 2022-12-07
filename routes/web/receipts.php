<?php

Route::group(['prefix' => 'sales-counter', 'as' => 'receipts.', 'middleware' => 'role:1|2|3|4|9'], function () {
    /**
     * Draft Receipts Routes
     */
    Route::get('drafts', [
        'as'   => 'drafts',
        'uses' => 'Receipts\DraftReceiptsController@index'
    ]);

    /**
     * Draft Receipts Routes
     */
    Route::get('drafts/{receiptKey}', [
        'as'   => 'draft',
        'uses' => 'Receipts\DraftRetailsController@show'
    ]);

    Route::post('add-receipt', [
        'as'   => 'add-receipt',
        'uses' => 'Receipts\DraftRetailsController@add'
    ]);

    Route::post('add-project-receipt', [
        'as'   => 'add-project-receipt',
        'uses' => 'Receipts\DraftProjectsController@add'
    ]);

    Route::post('drafts/{receiptKey}', [
        'as'   => 'draft-store',
        'uses' => 'Receipts\DraftRetailsController@store'
    ]);

    Route::post('drafts/project/{receiptKey}', [
        'as'   => 'draft-project-store',
        'uses' => 'Receipts\DraftProjectsController@store'
    ]);

    Route::patch('drafts/retail/{receiptKey}', [
        'as'   => 'draft-update',
        'uses' => 'Receipts\DraftRetailsController@update'
    ]);

    Route::patch('drafts/project/{receiptKey}', [
        'as'   => 'draft-project-update',
        'uses' => 'Receipts\DraftProjectsController@update'
    ]);

    Route::delete('remove-receipt/{receiptKey}', [
        'as'   => 'remove-receipt',
        'uses' => 'Receipts\DraftRetailsController@destroy'
    ]);
    Route::post('get-charge-calculation', [
        'as'   => 'get-charge-calculation',
        'uses' => 'Api\DraftReceiptsController@getChargeCalculation'
    ]);

    Route::post('receipts/get-customer-data', [
        'as'   => 'get-customer-data',
        'uses' => 'Api\DraftReceiptsController@getCustomerData',
    ]);

    /**
     * Draft Receipt Items Routes
     */
    Route::post('drafts/{receiptKey}/items', [
        'as'   => 'draft-items',
        'uses' => 'Receipts\DraftRetailsController@draftItems'
    ]);
    Route::patch('drafts/{receiptKey}/items/{key}', [
        'as'   => 'draft-items-update',
        'uses' => 'Receipts\DraftRetailsController@draftItemsUpdate'
    ]);
    Route::delete('drafts/{receiptKey}/items/{key}', [
        'as'   => 'draft-items-delete',
        'uses' => 'Receipts\DraftRetailsController@draftItemsDelete'
    ]);
});

Route::group(['prefix' => 'receipts', 'namespace' => 'Receipts', 'as' => 'receipts.', 'middleware' => 'auth'], function () {
    /**
     * Receipts Routes
     */
    Route::get('search', ['as' => 'search', 'uses' => 'ReceiptsController@search']);
    Route::patch('problem-notes-update', ['as' => 'problem-notes-update', 'uses' => 'ReceiptsController@problemNotesUpdate']);
    Route::get('{receipt}/items', ['as' => 'items', 'uses' => 'ReceiptsController@items']);
    Route::get('{receipt}/costs-detail', ['as' => 'costs-detail', 'uses' => 'ReceiptsController@costsDetail']);
    Route::get('{receipt}/progress', ['as' => 'progress', 'uses' => 'ReceiptsController@progress']);
    Route::get('{receipt}/manifests', ['as' => 'manifests', 'uses' => 'ReceiptsController@manifests']);
    Route::get('{receipt}/couriers', ['as' => 'couriers', 'uses' => 'ReceiptsController@couriers']);
    Route::get('{receipt}/pdf', ['as' => 'pdf', 'uses' => 'ReceiptsController@pdf']);
    Route::get('{receipt}/pdf_v2', ['as' => 'pdf_v2', 'uses' => 'ReceiptsController@pdfV2']);
    Route::get('{receipt}/pdf-items-label', ['as' => 'pdf-items-label', 'uses' => 'ReceiptsController@pdfItemsLabel']);
    Route::get('{receipt}/pod', ['as' => 'pod', 'uses' => 'ReceiptsController@pod']);
    Route::get('{receipt}/edit', ['as' => 'edit', 'uses' => 'ReceiptsController@edit']);
    Route::get('{receipt}/delete', ['as' => 'delete', 'uses' => 'ReceiptsController@delete']);
    Route::patch('{receipt}', ['as' => 'update', 'uses' => 'ReceiptsController@update']);
    Route::delete('{receipt}', ['as' => 'destroy', 'uses' => 'ReceiptsController@destroy']);
    // Route::resource('retail', 'RetailController', ['except' => 'create', 'parameters' => ['retail' => 'receipt']]);
});

Route::group(['namespace' => 'Receipts', 'middleware' => 'role:1|5|7'], function () {
    /**
     * POD Routes
     */
    Route::get('pod/by-manifest', ['as' => 'pods.by-manifest', 'uses' => 'PodsController@byManifest']);
    Route::get('pod/by-receipt', ['as' => 'pods.by-receipt', 'uses' => 'PodsController@byReceipt']);
    Route::post('pod/{receiptId}', ['as' => 'pods.store', 'uses' => 'PodsController@store']);
    Route::patch('receipts/{receipt}/pod-update/{progress}', ['as' => 'pods.update', 'uses' => 'PodsController@update']);
    Route::patch('pod/{manifest}/receive-manifest', ['as' => 'pods.receive-manifest', 'uses' => 'PodsController@receiveManifest']);

    /**
     * Returning Receipts
     */
    Route::get('receipts/returning-receipts', ['as' => 'receipts.returnings.index', 'uses' => 'ReturningsController@index']);
    Route::post('receipts/returning-receipts', ['as' => 'receipts.returnings.store', 'uses' => 'ReturningsController@store']);
    Route::post('receipts/returning-receipts/remove', ['as' => 'receipts.returnings.remove', 'uses' => 'ReturningsController@remove']);
    Route::patch('receipts/returning-receipts/set-all-returned', ['as' => 'receipts.returnings.set-returned', 'uses' => 'ReturningsController@setAllReturned']);
    Route::delete('receipts/returning-receipts/destroy', ['as' => 'receipts.returnings.destroy', 'uses' => 'ReturningsController@destroy']);
});

Route::group(['prefix' => 'receipts', 'namespace' => 'Receipts', 'as' => 'receipts.', 'middleware' => 'auth'], function () {
    /**
     * Receipts Routes
     */
    Route::patch('{receipt}/customer-update', ['as' => 'customer-update.store', 'uses' => 'ReceiptsController@customerUpdate']);
});
