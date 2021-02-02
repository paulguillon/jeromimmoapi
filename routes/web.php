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
    return view('index');
});
$router->get('/profile', function () use ($router) {
    return view('profile');
});

// API route group
$router->group(['prefix' => 'api/v1'], function () use ($router) {

    // Matches "/api/v1/login
    $router->post('login', 'UserController@login');

    // Matches "/api/v1/register
    $router->post('register', 'UserController@register');

    // Matches "/api/v1/logout
    $router->post('logout', 'UserController@logout');


    //Routes roles
    $router->group(['prefix' => 'roles'], function () use ($router) {
        // Matches "/api/v1/roles/{id}
        $router->get('/{id}', 'RoleController@oneUserRole');

        // Matches "/api/v1/roles
        $router->post('/', 'RoleController@createRole');

        // Matches "/api/v1/roles/id
        $router->put('/{id}', 'RoleController@updateAll');

        // Matches "/api/v1/roles/id
        $router->patch('/{id}', 'RoleController@update');

        // Matches "/api/v1/roles/id
        $router->delete('/{id}', 'RoleController@delete');
    });

    //Routes users
    $router->group(['prefix' => 'users'], function () use ($router) {
        // Matches "/api/v1/users
        $router->get('/', 'UserController@allUsers');

        // Matches "/api/v1/users/id Get One
        $router->get('/{id}', 'UserController@oneUser');

        // Matches "/api/v1/users/id Put all of One User
        $router->put('/{id}', 'UserController@put');

        // Matches "/api/v1/users/id
        $router->patch('/{id}', 'UserController@patch');

        // Matches "/api/v1/users/id
        $router->delete('/{id}', 'UserController@delete');
    });

    //Routes properties
    $router->group(['prefix' => 'properties'], function () use ($router) {
        // Matches "/api/properties
        $router->get('/', 'PropertyController@allProperties');

        // Matches "/api/properties/id
        $router->get('/{id}', 'PropertyController@oneProperty');

        // Matches "/api/v1/properties
        $router->post('/', 'PropertyController@registerProperty');

        // Matches "/api/v1/properties/id
        $router->put('/{id}', 'PropertyController@put');

        // Matches "/api/v1/properties/id
        $router->patch('/{id}', 'PropertyController@patch');

        // Matches "/api/v1/properties/id
        $router->delete('/{id}', 'PropertyController@delete');
    });

    //Routes agency
    $router->group(['prefix' => 'agency'], function () use ($router) {
        // Matches "/api/agency
        $router->get('/', 'AgencyController@allAgency');

        // Matches "/api/agency/id
        $router->get('/{id}', 'AgencyController@oneAgency');

        // Matches "/api/v1/agency
        $router->post('/', 'AgencyController@registerAgency');

        // Matches "/api/agency/id
        $router->put('/{id}', 'AgencyController@put');

        // Matches "/api/agency/id
        $router->patch('/{id}', 'AgencyController@patch');

        // Matches "/api/v1/agency/id
        $router->delete('/{id}', 'AgencyController@delete');
    });

    //Routes FAQ
    $router->group(['prefix' => 'faq'], function () use ($router) {
        // Matches "/api/faq
        $router->get('/', 'FaqController@allFaq');

        // Matches "/api/agency/id
        $router->get('/{id}', 'FaqController@oneFaq');

        // Matches "/api/v1/faq
        $router->post('/', 'FaqController@registerFaq');

    // Matches "/api/v1/faq/id
    $router->put('faq/{id}', 'FaqController@put');

    // Matches "/api/v1/faq/id
    $router->patch('faq/{id}', 'FaqController@patch');

        // Matches "/api/v1/faq/id
        $router->delete('/{id}', 'FaqController@delete');
    });

    //Routes document
    $router->group(['prefix' => 'document'], function () use ($router) {
        // Matches "/api/document
        $router->get('/', 'DocumentController@allDocument');

        // Matches "/api/document/id
        $router->get('/{id}', 'DocumentController@oneDocument');

        // Matches "/api/v1/document
        $router->post('/', 'DocumentController@registerDocument');

        // Matches "/api/v1/document/id
        $router->put('/{id}', 'DocumentController@updateAll');

        // Matches "/api/v1/document/id
        $router->patch('/{id}', 'DocumentController@update');

        // Matches "/api/v1/document/id
        $router->delete('/{id}', 'DocumentController@delete');
    });

    //Routes visit
    $router->group(['prefix' => 'visit'], function () use ($router) {
        // Matches "/api/visit
        $router->get('/', 'VisitController@allVisit');

        // Matches "/api/visit/id
        $router->get('/{id}', 'VisitController@oneVisit');

        // Matches "/api/v1/visit
        $router->post('/', 'VisitController@registerVisit');

        // Matches "/api/v1/visit/id
        $router->put('/{id}', 'VisitController@updateAll');

        // Matches "/api/v1/visit/id
        $router->patch('/{id}', 'VisitController@update');

        // Matches "/api/v1/visit/id
        $router->delete('/{id}', 'VisitController@delete');
    });
});
