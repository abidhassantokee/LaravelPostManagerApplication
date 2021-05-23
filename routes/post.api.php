<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'v1/'
], function () {
    Route::get('posts', 'App\Http\Controllers\V1\PostController@index');
    Route::get('posts/{id}', 'App\Http\Controllers\V1\PostController@show');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('posts', 'App\Http\Controllers\V1\PostController@store');
        Route::put('posts/{id}', 'App\Http\Controllers\V1\PostController@update');
        Route::delete('posts/{id}', 'App\Http\Controllers\V1\PostController@destroy');
    });
});
