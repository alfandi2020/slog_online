<?php

Route::get('provinces', ['as' => 'regions.provinces', 'uses' => 'RegionsController@provinces']);
Route::get('cities', ['as' => 'regions.cities', 'uses' => 'RegionsController@cities']);
Route::get('districts', ['as' => 'regions.districts', 'uses' => 'RegionsController@districts']);
Route::get('destination-districts', ['as' => 'regions.destination-districts', 'uses' => 'RegionsController@destinationDistricts']);