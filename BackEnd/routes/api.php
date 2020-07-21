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
        'prefix' => 'emitente'
    ], function () {
        Route::get('', 'EmitenteController@index');
        Route::post('', 'EmitenteController@create');

        //configs
        Route::get('config/{id}', 'EmitenteController@configDados');
        Route::put('config/{id}', 'EmitenteController@configUpdate');
        Route::put('certificate/{id}', 'EmitenteController@configCertificate');

        Route::get('{id}', 'EmitenteController@show');
        Route::put('{id}', 'EmitenteController@update');
        Route::delete('{id}', 'EmitenteController@destroy');
    });

    Route::group([
        'prefix' => 'product'
    ], function () {
        Route::get('', 'ProductController@index');
        Route::post('', 'ProductController@create');
        Route::post('mov_estoque', 'ProductController@mov_estoque');
        Route::get('{id}', 'ProductController@show');
        Route::put('{id}', 'ProductController@update');
        Route::delete('{id}', 'ProductController@destroy');
    });

    Route::group([
        'prefix' => 'venda'
    ], function () {
        Route::get('', 'VendaController@index');
        Route::post('', 'VendaController@create');
        Route::post('gera_nfe', 'VendaController@geraNFe');
        Route::put('set-client/{venda_id}', 'VendaController@setClient');
        Route::get('{id}', 'VendaController@show');
        Route::put('{id}', 'VendaController@update');
        Route::delete('{id}', 'VendaController@destroy');
    });

    Route::group([
        'prefix' => 'venda_itens'
    ], function () {
        Route::post('', 'VendaItensController@create');
        Route::get('{venda_id}', 'VendaItensController@index');
        Route::put('{item_id}', 'VendaItensController@update');
        Route::delete('{item_id}', 'VendaItensController@destroy');
    });

    Route::group([
        'prefix' => 'user'
    ], function () {
        Route::get('', 'UserController@index');
        Route::post('', 'UserController@create');
        Route::get('permissions/', 'UserController@list_permissions');
        Route::post('permissions/', 'UserController@create_permissions');
        Route::put('permissions/{id}', 'UserController@update_permissions');
        Route::get('permissions/{id}', 'UserController@show_permissions');
        Route::get('{id}', 'UserController@show');
        Route::put('{id}', 'UserController@update');
        Route::delete('{id}', 'UserController@destroy');
    });

    Route::group([
        'prefix' => 'payment'
    ], function () {
        Route::get('', 'PaymentController@index');
        Route::post('', 'PaymentController@create');
        Route::get('{id}', 'PaymentController@show');
        Route::put('{id}', 'PaymentController@update');
        Route::delete('{id}', 'PaymentController@destroy');
    });

    Route::group([
        'prefix' => 'nfe'
    ], function () {
        Route::get('', 'NFeController@index');
        Route::post('', 'NFeController@create');

        Route::post('emitir', 'NFeController@transmitir');
        Route::post('cancelar', 'NFeController@cancelar');

        Route::get('itens', 'NFeController@index_item');
        Route::post('itens', 'NFeController@create_item');
        Route::put('itens/{id}', 'NFeController@update_item');
        Route::delete('itens/{id}', 'NFeController@destroy_item');

        Route::post('payment', 'NFeController@create_payment');
        Route::delete('payment/{id}', 'NFeController@destroy_payment');

        Route::get('print/{id}', 'NFeController@print');
        Route::get('{id}', 'NFeController@show');
        Route::put('{id}', 'NFeController@update');
        Route::post('{id}', 'NFeController@destroy');
    });

    Route::group([
        'prefix' => 'nfce'
    ], function () {
        Route::get('', 'NFCeController@index');
        Route::post('', 'NFCeController@create');
        Route::post('print', 'NFCeController@print');
        Route::get('{id}', 'NFCeController@show');
        Route::put('{id}', 'NFCeController@update');
        Route::post('{id}', 'NFCeController@destroy');
    });

    Route::group([
        'prefix' => 'financeiro'
    ], function () {
        Route::get('caixa', 'FinanceiroController@resumoCaixa');
    });

    Route::group([
        'prefix' => 'fiscal'
    ], function () {
        Route::post('getMeses', 'FiscalController@index');
        Route::post('sendXML', 'FiscalController@sendXML');
    });
});
