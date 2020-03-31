<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('registration', 'AuthController@registration');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('recovery', 'ForgotPasswordController@recovery');
    Route::post('reset', 'ResetPasswordController@reset')->name('password.reset');
});

// Lessons
Route::group(['prefix' => 'lessons'], function () {
    Route::get('/', 'LessonController@index');
    Route::get('/{lesson}', 'LessonController@show');
    Route::post('/', 'LessonController@store');
    Route::put('/{lesson}', 'LessonController@update');
    Route::delete('/{lesson}', 'LessonController@destroy');
});

// Student
Route::group(['prefix' => 'student'], function () {
    Route::get('/', 'StudentController@index');
    Route::get('/{student}', 'StudentController@show');
    Route::post('/', 'StudentController@store');
    Route::put('/{student}', 'StudentController@update');
    Route::delete('/{student}', 'StudentController@destroy');
});