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



$router->group(['prefix' => 'api/'], function () use ($router) {

    $router->get('test-email', 'AuthController@testEmail');

    $router->group(['prefix' => 'auth/'], function () use ($router) {
        $router->post('login', 'AuthController@login');
        $router->post('register', 'AuthController@register');
        $router->get('profile', 'AuthController@getUser');
        $router->get('active-account/{hash}', 'AuthController@activeAccount');
        $router->post('forgot-password', 'AuthController@forgotPassword');
        $router->post('restore-password', 'AuthController@restorePassword');
    });

    $router->group(['prefix' => 'shipments'], function () use ($router) {

        $router->get('/', 'ShipmentController@index');
        $router->post('/', 'ShipmentController@store');
        $router->post('/create-label', 'ShipmentController@createLabel');
        $router->get('/{id}', 'ShipmentController@show');
        $router->delete('/{id}', 'ShipmentController@destroy');

    });

    $router->group(['prefix' => 'shipments'], function () use ($router) {

        $router->get('/', 'ShipmentController@index');
        $router->post('/', 'ShipmentController@store');
        $router->get('/{id}', 'ShipmentController@show');
        $router->delete('/{id}', 'ShipmentController@destroy');

    });

    $router->group(['prefix' => 'packages'], function () use ($router) {

        $router->get('/', 'PackageController@index');
        $router->post('/', 'PackageController@store');
        $router->get('/{id}', 'PackageController@show');
        $router->put('/{id}', 'PackageController@update');
        $router->delete('/{id}', 'PackageController@destroy');


    });

    $router->group(['prefix' => 'countries'], function () use ($router) {
        $router->get('/', 'CountryController@index');
    });

    $router->group(['prefix' => 'recharges'], function () use ($router) {
        $router->get('/', 'RechargeController@index');
        $router->post('/', 'RechargeController@makePayment');
    });

    $router->group(['prefix' => 'ezcmd'], function () use ($router) {
        $router->post('/get-locations', 'EZCMDController@getLocations');
    });

    $router->group(['prefix' => 'locations'], function () use ($router) {

        $router->get('/{id}', 'LocationController@show');
        $router->get('/origenes', 'LocationController@getOrigenes');
        $router->get('/destinations', 'LocationController@getDestinations');
        //


    });

    $router->group(['prefix' => 'srenvio'], function () use ($router) {

        $router->post('/quote', 'SrEnvioController@quote');


    });
});
