<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::get('/testes', function () {
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    return "Cache is cleared";
});

Route::group([
    'prefix' => 'admin'
], function () {

    Route::group([
        'prefix' => 'busness'
    ], function () {
        Route::get('', 'EmpresaController@index');
        Route::post('', 'EmpresaController@create');
        Route::get('{id}', 'EmpresaController@show');
        Route::put('{id}', 'EmpresaController@update');
        Route::delete('{id}', 'EmpresaController@destroy');
    });

    Route::group([
        'prefix' => 'users'
    ], function () {
        Route::get('', 'EmpresaController@getUsers');
        Route::post('', 'EmpresaController@createUser');
        Route::get('{id}', 'EmpresaController@showUser');
        Route::put('{id}', 'EmpresaController@updateUser');
        Route::delete('{id}', 'EmpresaController@destroyUser');
    });
});

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
        Route::post('config', 'EmitenteController@configCreate');
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

        Route::group([
            'prefix' => 'variation'
        ], function () {
            Route::get('', 'ProductController@variationList');
        });

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
        Route::get('print/{id}', 'VendaController@printSale');
        Route::put('set-client/{venda_id}', 'VendaController@setClient');
        Route::post('cancel/{id}', 'VendaController@cancel');
        Route::post('estorno/{id}', 'VendaController@estorno_venda');
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

        Route::group([
            'prefix' => 'references'
        ], function () {
            Route::get('', 'NFeController@search_references');
            Route::post('', 'NFeController@create_references');
            Route::get('{id}', 'NFeController@get_references');
            Route::delete('{id}', 'NFeController@remove_references');
        });

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
        Route::delete('{id}', 'NFeController@destroy');
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

        Route::group([
            'prefix' => 'contas-pagar'
        ], function () {
            Route::get('', 'ContasPagarController@index');
            Route::post('', 'ContasPagarController@create');
            Route::post('paymentConta', 'ContasPagarController@paymentConta');
            Route::get('{id}', 'ContasPagarController@show');
            Route::put('{id}', 'ContasPagarController@update');
            Route::delete('{id}', 'ContasPagarController@destroy');
        });
        Route::group([
            'prefix' => 'contas-receber'
        ], function () {
            Route::get('', 'ContasReceberController@index');
            Route::post('', 'ContasReceberController@create');
            Route::post('paymentConta', 'ContasReceberController@paymentConta');
            Route::get('{id}', 'ContasReceberController@show');
            Route::put('{id}', 'ContasReceberController@update');
            Route::delete('{id}', 'ContasReceberController@destroy');
        });

        Route::get('caixa', 'FinanceiroController@resumoCaixa');
        Route::get('payments', 'FinanceiroController@listPayments');
        Route::post('createPayment', 'FinanceiroController@createPayment');
        Route::get('showPayment/{id}', 'FinanceiroController@getByIdPayment');
        Route::put('updatePayment/{id}', 'FinanceiroController@updatePayment');
        Route::delete('payments/{id}', 'FinanceiroController@deletePayments');
    });

    Route::group([
        'prefix' => 'fiscal'
    ], function () {
        Route::post('getMeses', 'FiscalController@index');
        Route::post('sendXML', 'FiscalController@sendXML');
    });

    Route::group([
        'prefix' => 'manifesto'
    ], function () {
        Route::get('', 'FiscalController@getListaNotas');
        Route::post('', 'FiscalController@getMonitorSefaz');
        Route::post('{id}', 'FiscalController@manifestaNFe');
    });

    Route::group([
        'prefix' => 'import'
    ], function () {
        Route::post('', 'FiscalController@getDadosXML');
        Route::post('{id}', 'FiscalController@importDadosXML');
    });
});


Route::group(['prefix' => 'fast-pdv'], function () {

    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::post('login', 'FastController@login');
    });

    Route::group([
        'prefix' => 'carga'
    ], function () {
        Route::post('push/{id}', 'FastController@pushCarga');
        Route::post('send/{id}', 'FastController@sendCarga');
    });

    Route::group([
        'prefix' => 'users'
    ], function () {
        Route::get('', 'EmpresaController@getUsers');
        Route::post('', 'EmpresaController@createUser');
        Route::get('{id}', 'EmpresaController@showUser');
        Route::put('{id}', 'EmpresaController@updateUser');
        Route::delete('{id}', 'EmpresaController@destroyUser');
    });
});
