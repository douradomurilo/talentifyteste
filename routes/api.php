<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::namespace('Api')->group(function() {
    /* Companies */
    Route::middleware('auth:api')->apiResource('companies', 'CompaniesController');
    
    /* Jobs */
    Route::get('jobs', 'JobsController@index')->name('jobs.index');
    Route::get('jobs/{job}', 'JobsController@show')->name('jobs.show');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::post('jobs', 'JobsController@store')->name('jobs.store');
        Route::match(['put', 'patch'], 'jobs/{job}', 'JobsController@update')->name('jobs.update');
        Route::delete('jobs/{job}', 'JobsController@destroy')->name('jobs.destroy');
    });

    /* Users */
    Route::post('users', 'UsersController@store')->name('users.store');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('users', 'UsersController@index')->name('users.index');
        Route::get('users/{user}', 'UsersController@show')->name('users.show');
        Route::match(['put', 'patch'], 'users/{user}', 'UsersController@update')->name('users.update');
        Route::delete('users/{user}', 'UsersController@destroy')->name('users.destroy');
    });
});
