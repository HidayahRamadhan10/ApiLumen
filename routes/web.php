<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => 'cors'], function() use ($router){   

    $router->post('/login', 'AuthController@login');
    $router->get('/logout', 'AuthController@logout');
    $router->get('/profile', 'AuthController@me');

    $router->group(['prefix' => 'stuff/'], function() use ($router){
        //statistic routes
        $router->get('/data', 'StuffController@index');
        $router->post('/', 'StuffController@store');
        $router->get('/trash', 'StuffController@trash');

        //dynamic routes
        $router->get('{id}', 'StuffController@show');
        $router->patch('/{id}', 'StuffController@update');
        $router->delete('/{id}', 'StuffController@destroy');
        $router->get('/restore/{id}', 'StuffController@restore');
        $router->delete('/permanent/{id}', 'StuffController@deletePermanent');
    });


    $router->group(['prefix' => 'user'], function() use ($router){  

        $router->get('/data', 'UserController@index');
        $router->post('/', 'UserController@store');
        $router->get('/trash', 'UserController@trash');

        //dynamic routes
        $router->get('{id}', 'UserController@show');
        $router->patch('/{id}', 'UserController@update');
        $router->delete('/{id}', 'UserController@destroy');
        $router->get('/restore/{id}', 'UserController@restore');
        $router->delete('/permanent/{id}', 'UserController@deletepermanent');
    });

    $router->group(['prefix' => 'inbound-stuff/', 'middleware' => 'auth'], function() use ($router){  

        $router->get('/data', 'InboundStuffController@index');
        $router->post('store', 'InboundStuffController@store');
        $router->get('detail/{id}', 'InboundStuffController@show');
        $router->patch('update/{id}', 'InboundStuffController@update');
        $router->delete('delete/{id}', 'InboundStuffController@destroy');
        $router->get('recycle-bin', 'InboundStuffController@recycleBin');
        $router->get('restore/{id}', 'InboundStuffController@restore');
        $router->delete('/permanent/{id}', 'InboundStuffController@deletepermanent');
    });

    $router->group(['prefix' => 'stuff-stock/', 'middleware' => 'auth'], function() use ($router){  

        $router->get('/', 'StuffStockController@index');
        // $router->post('store', 'StuffStockController@store');
        // $router->get('detail/{id}', 'StuffStockController@show');
        // $router->patch('update/{id}', 'StuffStockController@update');
        // $router->delete('delete/{id}', 'StuffStockController@destroy');
        // $router->get('recycle-bin', 'StuffStockController@recycleBin');
        // $router->get('restore/{id}', 'StuffStockController@restore');
        // $router->delete('/permanent/{id}', 'StuffStockController@deletepermanent');
        $router->post('add-stock/{id}', 'StuffStockController@addstock');
        // $router->post('sub-stock/{id}', 'StuffStockController@substock');
    });

    $router->group(['prefix' => 'lending'], function() use ($router){  

        $router->get('/data', 'LendingController@index');
        $router->post('store', 'LendingController@store');
        $router->get('show/{id}', 'LendingController@show');
        $router->patch('update/{id}', 'LendingController@update');
        $router->delete('delete/{id}', 'LendingController@destroy');
        $router->get('/trash', 'LendingController@trash');
        $router->delete('/permanent/{id}', 'LendingController@deletepermanent');
    });

    $router->group(['prefix' => 'restoration'], function() use ($router){  

        $router->get('/data', 'RestorationController@index');
        $router->post('store', 'RestorationController@store');
        $router->get('detail/{id}', 'RestorationController@show');
        $router->patch('update/{id}', 'RestorationController@update');
    });

});