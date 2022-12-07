<?php

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'role:1'], function () {
    /**
     * Networks Routes
     */
    Route::get('networks/{network}/users', ['as' => 'networks.users', 'uses' => 'NetworksController@users']);
    Route::get('networks/{network}/delivery-units', ['as' => 'networks.delivery-units', 'uses' => 'NetworksController@deliveryUnits']);
    Route::post('networks/{network}/delivery-unit-store', ['as' => 'networks.delivery-unit-store', 'uses' => 'NetworksController@deliveryUnitStore']);
    Route::patch('networks/{network}/delivery-unit-update/{delivery_unit}', ['as' => 'networks.delivery-unit-update', 'uses' => 'NetworksController@deliveryUnitUpdate']);
    Route::delete('networks/{network}/delivery-unit-destroy/{delivery_unit}', ['as' => 'networks.delivery-unit-destroy', 'uses' => 'NetworksController@deliveryUnitDestroy']);
    Route::get('networks/{network}/customers', ['as' => 'networks.customers', 'uses' => 'NetworksController@customers']);
    Route::get('networks/{network}/delete', ['as' => 'networks.delete', 'uses' => 'NetworksController@delete']);
    Route::resource('networks', 'NetworksController');

    /**
     * Comodities Routes
     */
    Route::resource('comodities', 'CustomerComoditiesController', ['except' => ['create', 'show', 'edit']]);

    /**
     * Users Routes
     */
    Route::get('users/search', ['as' => 'users.search', 'uses' => 'UsersController@search']);
    Route::resource('users', 'UsersController');

    /**
     * Package Types Routes
     */
    Route::resource('package-types', 'PackageTypesController', ['except' => ['create', 'show', 'edit']]);

    /**
     * Regions Routes
     */
    Route::get('regions', ['as' => 'regions.provinces', 'uses' => 'RegionsController@provinces']);
    Route::get('regions/cities', ['as' => 'regions.cities', 'uses' => 'RegionsController@cities']);
    Route::get('regions/districts', ['as' => 'regions.districts', 'uses' => 'RegionsController@districts']);

    /**
     * Delivery Units Routes
     */
    // Route::resource('delivery-units', 'DeliveryUnitsController');

    /**
     * Backup Restore Database Routes
     */
    Route::post('backups/upload', ['as' => 'backups.upload', 'uses' => 'BackupsController@upload']);
    Route::post('backups/{fileName}/restore', ['as' => 'backups.restore', 'uses' => 'BackupsController@restore']);
    Route::get('backups/{fileName}/dl', ['as' => 'backups.download', 'uses' => 'BackupsController@download']);
    Route::resource('backups', 'BackupsController');

    Route::get('monitorings/log-files', ['as' => 'log-files.index', 'uses' => 'LogFilesController@index']);
    Route::get('monitorings/log-files/{filename}', ['as' => 'log-files.show', 'uses' => 'LogFilesController@show']);
    Route::get('monitorings/log-files/{filename}/download', ['as' => 'log-files.download', 'uses' => 'LogFilesController@download']);
});
