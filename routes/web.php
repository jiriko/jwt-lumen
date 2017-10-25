<?php

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Http\Controllers'], function($api) {

        $api->group(['prefix' => 'users'], function () use ($api) {
            $api->post('login', ['uses' => 'Auth\LoginController@login', 'as' => 'login.store']);
            $api->post('refresh-token', ['uses' => 'Auth\LoginController@refreshToken', 'as' => 'login.refresh']);
        });

        //Let's refresh the token with every request and
        //add a blacklist grace period of two minutes
        //for concurrent requests to not fail
        $api->group(['middleware' => ['jwt.refresh','api.auth']], function () use ($api) {

            $api->resource('enrollments','EnrollmentsController', ['only' => ['destroy','store']]);

            $api->resource('students','StudentsController', ['only' => ['index','update','destroy','store']]);

            $api->resource('subjects','SubjectsController', ['only' => ['index','update','destroy','store']]);

            //used for xeditable
            $api->get('validation', ['uses' => 'ValidationController', 'as' => 'validation']);
        });

    });
});