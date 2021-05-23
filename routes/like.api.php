<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'v1/'
], function () {
    Route::get('posts/{id}/likes', 'App\Http\Controllers\V1\LikeController@getLikesByPost');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('posts/{id}/likes', 'App\Http\Controllers\V1\LikeController@store');
        Route::delete('posts/{postId}/likes/{likeId}', 'App\Http\Controllers\V1\LikeController@destroy');
    });
});
