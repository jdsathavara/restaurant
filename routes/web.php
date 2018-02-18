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
    //return $router->app->version();
    return '<h1><marquee>Truck API</marquee></h1>';
});
$router->group(['namespace' => 'API'], function() use ($router) {
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('login','AuthController@login');
        $router->post('register','AuthController@register');
        $router->post('verifyMobile/{id}','AuthController@verifyMobile');
        $router->put('forgotPassword','AuthController@forgotPassword');
        $router->put('resetPassword','AuthController@resetPassword');
    });
    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->put('{id}/updateProfile','UserController@updateProfile');
        $router->put('{id}/logout','UserController@logout');
        $router->put('{id}/sendCode','UserController@sendCode');
		$router->get('foodTrucks','FoodTruckController@allUserTrucks');
		$router->post('{id}/foodTrucks','FoodTruckController@addFoodTruck');
		$router->post('foodTrucks/{id}','FoodTruckController@editFoodTruck');
		$router->get('foodTrucks/{id}/category','FoodTruckCategoryController@getTruckCategory');
		$router->post('foodTrucks/{id}/category','FoodTruckCategoryController@addCategory');
		$router->put('category/{id}','FoodTruckCategoryController@updateCategory');
		$router->delete('category/{id}','FoodTruckCategoryController@deleteCategory');
		$router->get('category/{id}','FoodTruckCategoryController@getCategory');
    });
	$router->group(['prefix' => 'product'], function () use ($router) {
        $router->post('addProduct','ProductController@addProduct');
    });
});

