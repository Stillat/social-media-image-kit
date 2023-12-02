<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'social-media-image-kit'], function () {
    Route::get('preview/{id}', '\Stillat\SocialMediaImageKit\Http\Controllers\SocialPreviewController@index')->name('social-media-image-kit.preview');
});
