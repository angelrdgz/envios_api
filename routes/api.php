<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['json.response']], function () {

    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('auth')->group(function () {

        Route::post('/login', 'Api\AuthController@login')->name('login.api');
        Route::post('/register', 'Api\AuthController@register')->name('register.api');
        Route::get('/active-account/{hash}', 'Api\AuthController@activeAccount');
        Route::post('/forgot-password', 'Api\AuthController@forgotPassword');
        Route::post('/restore-password', 'Api\AuthController@restorePassword');

        // private routes
        Route::middleware('auth:api')->group(function () {
            Route::get('/business-info', 'Api\AuthController@getBusinessInfo');
            Route::post('/business-info', 'Api\AuthController@businessInfo');
            Route::get('/profile', 'Api\AuthController@getUser');
            Route::get('/logout', 'Api\AuthController@logout')->name('logout');
        });
    });

    Route::prefix('shipments')->group(function () {
        Route::get('/', 'ShipmentController@index');
        Route::post('/', 'ShipmentController@store');
        Route::post('/create-label', 'ShipmentController@createLabel');
        Route::get('/{id}', 'ShipmentController@show');
        Route::delete('/{id}', 'ShipmentController@destroy');
    });

    Route::prefix('packages')->group(function () {
        Route::get('/', 'PackageController@index');
        Route::post('/', 'PackageController@store');
        Route::get('/{id}', 'PackageController@show');
        Route::put('/{id}', 'PackageController@update');
        Route::delete('/{id}', 'PackageController@destroy');
    });

    Route::prefix('locations')->group(function () {
        Route::get('/origenes', 'PointController@getOrigenes');
        Route::get('/destinations', 'PointController@getDestinations');
        Route::get('/{id}', 'PointController@show');
    });

    Route::prefix('recharges')->group(function () {
        Route::get('/', 'RechargeController@index');
        Route::post('/', 'RechargeController@makePayment');
        Route::post('/{id}/invoice', 'RechargeController@creatInvoice');
    });

    Route::prefix('countries')->group(function () {
        Route::get('/', 'CountryController@index');
    });

    Route::prefix('states')->group(function () {
        Route::get('/', 'StateController@index');
    });

    Route::prefix('rates')->group(function () {
        Route::post('/', 'RateController@store');
    });

    Route::prefix('tracking')->group(function () {
        Route::get('/{id}', 'TrackingController@show');
    });

    Route::prefix('ezcmd')->group(function () {
        Route::post('/get-locations', 'EZCMDController@getLocations');
    });

    Route::prefix('carriers')->group(function () {
        Route::get('/', 'CarrierController@index');
    });

    Route::prefix('invoices')->group(function () {
        Route::get('/', 'InvoiceController@index');
        Route::post('/', 'InvoiceController@store');
        Route::get('/{id}', 'InvoiceController@show');
        Route::delete('/{id}', 'InvoiceController@destroy');
    });

    

    

    

    // public routes


});

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->group(function () {
    Route::get('users', function () {
        // Matches The "/admin/users" URL
    });
});

Route::prefix('auth')->group(function (){
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::get('profile', 'AuthController@getUser');
    Route::get('active-account/{hash}', 'AuthController@activeAccount');
    Route::post('forgot-password', 'AuthController@forgotPassword');
    Route::post('restore-password', 'AuthController@restorePassword');
});*/
