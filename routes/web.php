<?php

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

$router->group(['prefix' => 'users'], function () use ($router) {
    $router->post('login', ['uses' => 'Auth\LoginController@login', 'as' => 'login.store']);
    $router->post('refresh-token', ['uses' => 'Auth\LoginController@refreshToken', 'as' => 'login.refresh']);
});

//Let's refresh the token with every request and
//add a blacklist grace period of two minutes
//for concurrent requests to not fail
$router->group(['middleware' => ['jwt.refresh']], function () use ($router) {

});