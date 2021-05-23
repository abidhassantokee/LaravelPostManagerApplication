<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'v1/'
], function () {
    Route::get('posts/{id}/comments', 'App\Http\Controllers\V1\CommentController@getCommentsByPost');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('posts/{id}/comments', 'App\Http\Controllers\V1\CommentController@store');
        Route::delete('posts/{postId}/comments/{commentId}', 'App\Http\Controllers\V1\CommentController@destroy');
    });
});
