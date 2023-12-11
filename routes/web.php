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

$router->get('/', function () {
    return 'Desafio Técnico Ultralinks! Acesse a collection do postman para consumir a API';
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('logout', 'AuthController@logout');
    $router->post('refresh', 'AuthController@refresh');
    $router->post('register', "UserController@createUser");

});
$router->post('deposits/', "DepositController@postMakeDeposit");

$router->group(['middleware' => 'auth:api'], function () use ($router) {
    $router->post('auth/me', 'AuthController@me');

    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', "UserController@getUsers");
        $router->get('/{userId}', "UserController@getUserById");
        $router->get('/document/{userDocument}', "UserController@getUserByDocument");
        $router->post('/', "UserController@createUser");
    });

    $router->group(['prefix' => 'deposits'], function () use ($router) {
        $router->get('/', "DepositController@getDeposits");
        $router->get('cod/{depositCod}', "DepositController@getDepositByCod");
        $router->get('id/{depositId}', "DepositController@getDepositById");
    });

    $router->group(['prefix' => 'transfers'], function () use ($router) {
        $router->get('/', "TransferController@getTransfers");
        $router->post('/', "TransferController@postMakeTransfer");
        $router->get('cod/{transferCod}', "TransferController@getTransferByCod");
        $router->get('id/{transferId}', "TransferController@getTransferById");
    });
});
