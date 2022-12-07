<?php

Route::group(['prefix' => 'v1','namespace' => 'Api', 'as' => 'api.', 'middleware' => ['auth:api']], function() {
    require __DIR__ . '/api/regions.php';
    require __DIR__ . '/api/costs.php';
    require __DIR__ . '/api/receipts.php';
});

Route::group(['prefix' => 'v1', 'as' => 'api.', 'middleware' => ['auth:api']], function() {
    Route::post('receipts/drafts/{receiptKey}/items', ['as' => 'receipts.draft-items', 'uses' => 'Receipts\DraftReceiptsController@draftItems']);
    Route::patch('receipts/drafts/{receiptKey}/items', ['as' => 'receipts.patch-draft-items', 'uses' => 'Receipts\DraftReceiptsController@patchDraftItems']);
});