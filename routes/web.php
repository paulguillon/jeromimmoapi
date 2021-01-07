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

// API route group
$router->group(['prefix' => 'api/v1'], function () use ($router) {

    // Matches "/api/v1/role
    $router->post('roles', 'RoleController@createRole');

    // Matches "/api/v1/role/{id}
    $router->get('roles/{id}', 'RoleController@oneUserRole');

    // Matches "/api/v1/users
    $router->get('users', 'UserController@allUsers');

    // Matches "/api/v1/users/id
    $router->get('users/{id}', 'UserController@oneUser');

    // Matches "/api/v1/register
    $router->post('register', 'UserController@register');

    // Matches "/api/properties
    $router->get('properties', 'PropertyController@allProperties');

    // Matches "/api/properties/id
    $router->get('properties/{id}', 'PropertyController@oneProperty');

    // Matches "/api/v1/registerProperty
    $router->post('/registerProperty', 'PropertyController@registerProperty');

    // Matches "/api/v1/login
    $router->post('login', 'UserController@login');
});
