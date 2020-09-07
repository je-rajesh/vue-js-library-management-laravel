<?php

use Illuminate\Http\Request;
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

Route::post('/login', 'Api\LoginController@login')->name('api.login');

Route::middleware('auth:api')->group(function(){
    Route::apiResource('books', 'Api\BooksController');
    Route::post('/logout', 'Api\LoginController@logout')->name('api.logout');
});
Route::get('/homes', function(){
    return 'hi from home';
});
