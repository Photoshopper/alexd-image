<?php

Route::group(['prefix' => 'image'], function() {
    Route::put('upload', ['as' => 'image.upload', 'uses' => 'ImageController@ajaxImageUpload']);
    Route::delete('delete', ['as' => 'image.delete', 'uses' => 'ImageController@ajaxImageDelete']);
    Route::post('crop.check', ['as' => 'image.crop.check', 'uses' => 'ImageController@ajaxCropCheck']);
    Route::post('crop.save', ['as' => 'image.crop.save', 'uses' => 'ImageController@ajaxCropSave']);
});

Route::group(['prefix' => 'images'], function() {
    Route::put('upload', ['as' => 'images.upload', 'uses' => 'ImageController@ajaxImagesUpload']);
});