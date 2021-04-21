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
    return redirect('/api/v1');
});

$router->group(['prefix' => 'api'], function () use ($router){

    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');

});

$router->group(['prefix' => 'api', 'middleware' => 'auth:api'], function () use ($router){

    $router->post('/logout', 'AuthController@logout');
    $router->get('/profile', 'AuthController@me');

    $router->get('/users','UsersController@index');
    $router->get('/users/{id}/balance','UsersController@balance');

    $router->post('/transactions/create','TransactionsController@store');
    $router->get('/transactions','TransactionsController@index');
    $router->get('/users/{id}/transactions','TransactionsController@userTransactions');
    $router->get('/transactions/{id}','TransactionsController@show');
    $router->get('/transactions/{id}/refund','TransactionsController@refund');

});
