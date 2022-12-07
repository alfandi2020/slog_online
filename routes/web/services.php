<?php

Route::group(['namespace' => 'Services', 'prefix' => 'services', 'middleware' => 'role:1|4'], function () {
    /**
     * Pickup Orders Routes
     */
    Route::patch('pickups/{pickup}/send', ['as' => 'pickups.send', 'uses' => 'PickupsController@send']);
    Route::patch('pickups/{pickup}/take-back', ['as' => 'pickups.take-back', 'uses' => 'PickupsController@takeBack']);
    Route::get('pickups/{pickup}/pdf', ['as' => 'pickups.pdf', 'uses' => 'PickupsController@pdf']);
    Route::get('pickups/{pickup}/receive', ['as' => 'pickups.receive', 'uses' => 'Pickups\ReceivedPickupsController@edit']);
    Route::patch('pickups/{pickup}/receive', ['as' => 'pickups.receive', 'uses' => 'Pickups\ReceivedPickupsController@update']);
    Route::delete('pickups/{pickup}/cancel-returned', ['as' => 'pickups.cancel-returned', 'uses' => 'Pickups\ReceivedPickupsController@destroy']);
    Route::resource('pickups', 'PickupsController');
});
