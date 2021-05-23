<?php

use Illuminate\Support\Facades\Route;

/**
 * User Routes
 */
Route::post('v1/register', 'App\Http\Controllers\V1\RegisterController@create');
Route::group([
    'prefix' => 'v1/auth'
], function () {
    Route::post('login', 'App\Http\Controllers\V1\AuthController@login');
    Route::get('logout', 'App\Http\Controllers\V1\AuthController@logout');
    Route::get('refresh', 'App\Http\Controllers\V1\AuthController@refresh');
    Route::get('profile', 'App\Http\Controllers\V1\AuthController@profile');
});
