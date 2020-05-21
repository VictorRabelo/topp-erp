<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('auth', 'AuthController@login');

Route::group([

   'middleware' => 'apiJWT',

], function () {

   Route::group([
      'prefix' => 'auth'
   ], function () {
      Route::post('logout', 'AuthController@logout');
      Route::post('refresh', 'AuthController@refresh');
      Route::get('me', 'AuthController@me');
   });

   Route::group([
      'prefix' => 'client'
   ], function () {
      Route::get('', 'ClientController@index');
      Route::post('', 'ClientController@create');
      Route::get('{id}', 'ClientController@show');
      Route::put('{id}', 'ClientController@update');
      Route::delete('{id}', 'ClientController@destroy');
   });

   Route::group([
      'prefix' => 'product'
   ], function () {
      Route::get('', 'ProductController@index');
      Route::post('', 'ProductController@create');
      Route::get('{id}', 'ProductController@show');
      Route::put('{id}', 'ProductController@update');
      Route::delete('{id}', 'ProductController@destroy');
   });
});
