<?php

Route::group(['namespace' => 'Manifests', 'middleware' => 'role:1|2|3|4|5|6'], function () {
    /**
     * Handover Manifests Routes
     */
    Route::get('manifests/handovers/{manifest}/pdf', ['as' => 'manifests.handovers.pdf', 'uses' => 'HandoversController@pdf']);
    Route::resource('manifests/handovers', 'HandoversController', [
        'as'         => 'manifests',
        'parameters' => ['handovers' => 'manifest'],
        // 'middleware' => 'role:1|3|4' // Admin, Sales Counter, and Warehouse
    ]);

    /**
     * Delivery Manifests Routes
     */
    Route::get('manifests/deliveries/{manifest}/pdf', ['as' => 'manifests.deliveries.pdf', 'uses' => 'DeliveriesController@pdf']);
    Route::resource('manifests/deliveries', 'DeliveriesController', [
        'as'         => 'manifests',
        'parameters' => ['deliveries' => 'manifest'],
        // 'middleware' => 'role:1|3|4' // Admin, Sales Counter, and Warehouse
    ]);

    /**
     * Distribution Manifests Routes
     */
    Route::patch('manifests/distributions/{manifestId}/send', [
        'as'   => 'manifests.distributions.send',
        'uses' => 'DistributionsController@send',
        // 'middleware' => 'role:1|3|4',
    ]);
    Route::get('manifests/distributions/{manifest}/xls', ['as' => 'manifests.distributions.xls', 'uses' => 'DistributionsController@xls']);
    Route::get('manifests/distributions/{manifest}/pdf', ['as' => 'manifests.distributions.pdf', 'uses' => 'DistributionsController@pdf']);
    Route::resource('manifests/distributions', 'DistributionsController', [
        'as'         => 'manifests',
        'parameters' => ['distributions' => 'manifest'],
        // 'middleware' => 'role:1|3|4|5' // Admin, Sales Counter, Warehouse, and Customer Service
    ]);

    /**
     * Return Manifests Routes
     */
    Route::get('manifests/returns/{manifest}/pdf', ['as' => 'manifests.returns.pdf', 'uses' => 'ReturnsController@pdf']);
    Route::resource('manifests/returns', 'ReturnsController', [
        'as'         => 'manifests',
        'parameters' => ['returns' => 'manifest'],
        // 'middleware' => 'role:1|5' // Admin, Customer Service
    ]);

    /**
     * Accounting Manifests Routes
     */
    Route::get('manifests/accountings/{manifest}/excel', ['as' => 'manifests.accountings.excel', 'uses' => 'AccountingsController@excel']);
    Route::get('manifests/accountings/{manifest}/html', ['as' => 'manifests.accountings.html', 'uses' => 'AccountingsController@html']);
    Route::get('manifests/accountings/{manifest}/pdf', ['as' => 'manifests.accountings.pdf', 'uses' => 'AccountingsController@pdf']);

    Route::post('manifests/accountings/{manifest}/assign-receipt', [
        'as'   => 'manifests.accountings.assign-receipt',
        'uses' => 'AccountingsController@assignReceipt',
        // 'middleware' => 'role:1|2|3|4|5'
    ]);

    Route::resource('manifests/accountings', 'AccountingsController', [
        'as'         => 'manifests',
        'parameters' => ['accountings' => 'manifest'],
        // 'middleware' => 'role:1|2|5' // Admin, Accounting, Customer Service
    ]);

    /**
     * Receipt Problem Manifests Routes
     */
    Route::get('manifests/problems/{manifest}/excel', ['as' => 'manifests.problems.excel', 'uses' => 'ProblemsController@excel']);
    Route::get('manifests/problems/{manifest}/html', ['as' => 'manifests.problems.html', 'uses' => 'ProblemsController@html']);
    Route::get('manifests/problems/{manifest}/pdf', ['as' => 'manifests.problems.pdf', 'uses' => 'ProblemsController@pdf']);
    Route::patch('manifests/problems/{manifestId}/send', [
        'as'   => 'manifests.problems.send',
        'uses' => 'ProblemsController@send',
    ]);
    Route::patch('manifests/problems/{manifestId}/patch-receive', [
        'as'   => 'manifests.problems.patch-receive',
        'uses' => 'ProblemsController@patchReceive',
    ]);
    Route::resource('manifests/problems', 'ProblemsController', [
        'as'         => 'manifests',
        'parameters' => ['problems' => 'manifest'],
        // 'middleware' => 'role:1|2|5' // Admin, Accounting, Customer Service
    ]);

    /**
     * Manifests Routes
     */
    Route::post('manifests/{manifestId}/assign-receipt', [
        'as'   => 'manifests.assign-receipt',
        'uses' => 'ManifestsController@assignReceipt',
        // 'middleware' => 'role:1|2|3|4|5'
    ]);
    Route::post('manifests/{manifestId}/remove-receipt', [
        'as'   => 'manifests.remove-receipt',
        'uses' => 'ManifestsController@removeReceipt',
        // 'middleware' => 'role:1|2|3|4|5'
    ]);
    Route::patch('manifests/{manifestId}/send', [
        'as'   => 'manifests.send',
        'uses' => 'ManifestsController@send',
        // 'middleware' => 'role:1|2|3|4|5'
    ]);
    Route::patch('manifests/{manifestId}/take-back', [
        'as'   => 'manifests.take-back',
        'uses' => 'ManifestsController@takeBack',
        // 'middleware' => 'role:1|2|3|4|5'
    ]);
    Route::get('manifests/{manifest}/receive', [
        'as'   => 'manifests.receive',
        'uses' => 'ManifestsController@receive',
        // 'middleware' => 'role:1|2|3|4|5'
    ]);
    Route::patch('manifests/{manifestId}/check-receipt', [
        'as'   => 'manifests.check-receipt',
        'uses' => 'ManifestsController@checkReceipt',
        // 'middleware' => 'role:1|2|3|4|5'
    ]);
    Route::patch('manifests/{manifestId}/reject-receipt', [
        'as'   => 'manifests.reject-receipt',
        'uses' => 'ManifestsController@rejectReceipt',
        // 'middleware' => 'role:1|2|3|4|5'
    ]);
    Route::patch('manifests/{manifestId}/patch-receive', [
        'as'   => 'manifests.patch-receive',
        'uses' => 'ManifestsController@patchReceive',
        // 'middleware' => 'role:1|2|3|4|5'
    ]);
    Route::resource('manifests', 'ManifestsController', ['except' => ['create', 'store', 'show', 'edit', 'update']]);
});
