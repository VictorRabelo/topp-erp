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

   Route::group([
      'prefix' => 'venda'
   ], function () {
      Route::get('', 'VendaController@index');
      Route::post('', 'VendaController@create');
      Route::get('{id}', 'VendaController@show');
      Route::put('{id}', 'VendaController@update');
      Route::delete('{id}', 'VendaController@destroy');
   });

   Route::group([
      'prefix' => 'user'
   ], function () {
      Route::get('', 'UserController@index');
      Route::post('', 'UserController@create');
      Route::get('permissions/{id}', 'UserController@show_permissions');
      Route::get('{id}', 'UserController@show');
      Route::put('{id}', 'UserController@update');
      Route::delete('{id}', 'UserController@destroy');
   });
});
