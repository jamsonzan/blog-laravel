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

//articles
Route::get('articles/index', 'ArticleController@index');
Route::get('articles/{id}', 'ArticleController@show');
Route::post('articles', 'ArticleController@store')->middleware('myAuth');
Route::put('articles/{id}', 'ArticleController@update')->middleware('myAuth');
Route::delete('articles/{id}', 'ArticleController@destroy')->middleware('myAuth');
//picture
Route::post('pictures', 'PictureController@store')->middleware('myAuth');
Route::delete('pictures/{id}', 'PictureController@destroy')->middleware('myAuth');
//comments
Route::get('comments/{id}', 'CommentController@index');
Route::post('comments', 'CommentController@store');
Route::delete('comments/{id}', 'CommentController@destroy')->middleware('myAuth');
//user
Route::get('users/setting', 'UserController@getSetting');
Route::post('users/setting', 'UserController@setSetting')->middleware('myAuth');
Route::post('login', 'UserController@login');
