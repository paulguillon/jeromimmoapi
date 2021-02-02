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

    // Matches "/api/v1/role Post
    $router->post('roles', 'RoleController@createRole');

    // Matches "/api/v1/roles/{id}
    $router->get('roles/{id}', 'RoleController@oneUserRole');

    // Matches "/api/v1/roles/id
    $router->put('roles/{id}', 'RoleController@updateAll');

    // Matches "/api/v1/roles/id
    $router->patch('roles/{id}', 'RoleController@update');

    // Matches "/api/v1/roles/id
    $router->delete('roles/{id}', 'RoleController@delete');

    // Matches "/api/v1/users
    $router->get('users', 'UserController@allUsers');

    // Matches "/api/v1/users/id Get One
    $router->get('users/{id}', 'UserController@oneUser');

    // Matches "/api/v1/users/id Put all of One User
    $router->put('users/{id}', 'UserController@put');

    // Matches "/api/v1/users/id
    $router->patch('users/{id}', 'UserController@patch');

    // Matches "/api/v1/users/id
    $router->delete('users/{id}', 'UserController@delete');

    // Matches "/api/v1/login
    $router->post('login', 'UserController@login');

    // Matches "/api/v1/register
    $router->post('register', 'UserController@register');

    // Matches "/api/v1/logout
    $router->post('logout', 'UserController@logout');

    // Matches "/api/properties
    $router->get('properties', 'PropertyController@allProperties');

    // Matches "/api/properties/id
    $router->get('properties/{id}', 'PropertyController@oneProperty');

    // Matches "/api/v1/properties/id
    $router->put('properties/{id}', 'PropertyController@put');

    // Matches "/api/v1/properties/id
    $router->patch('properties/{id}', 'PropertyController@patch');

    // Matches "/api/v1/properties/id
    $router->delete('properties/{id}', 'PropertyController@delete');

    // Matches "/api/v1/property
    $router->post('/property', 'PropertyController@registerProperty');

    // Matches "/api/agency
    $router->get('/agency', 'AgencyController@allAgency');

    // Matches "/api/agency/id
    $router->get('agency/{id}', 'AgencyController@oneAgency');

    // Matches "/api/agency/id
    $router->put('agency/{id}', 'AgencyController@put');

    // Matches "/api/agency/id
    $router->patch('agency/{id}', 'AgencyController@patch');

    // Matches "/api/agency/id
    $router->patch('agency/{id}', 'AgencyController@delete');

    // Matches "/api/v1/agency
    $router->post('/agency', 'AgencyController@registerAgency');

    // Matches "/api/v1/agency/id
    $router->delete('agency/{id}', 'AgencyController@delete');

    // Matches "/api/faq
    $router->get('faq', 'FaqController@allFaq');

    // Matches "/api/agency/id
    $router->get('faq/{id}', 'FaqController@oneFaq');

    // Matches "/api/v1/faq
    $router->post('/faq', 'FaqController@registerFaq');

    // Matches "/api/v1/faq/id
    $router->put('faq/{id}', 'FaqController@updateAll');

    // Matches "/api/v1/faq/id
    $router->patch('faq/{id}', 'FaqController@update');

    // Matches "/api/v1/faq/id
    $router->delete('faq/{id}', 'FaqController@delete');

    // Matches "/api/document
    $router->get('document', 'DocumentController@allDocument');

    // Matches "/api/document/id
    $router->get('document/{id}', 'DocumentController@oneDocument');

    // Matches "/api/v1/document/id
    $router->put('document/{id}', 'DocumentController@updateAll');

    // Matches "/api/v1/document/id
    $router->patch('document/{id}', 'DocumentController@update');

    // Matches "/api/v1/document/id
    $router->delete('document/{id}', 'DocumentController@delete');

    // Matches "/api/v1/document
    $router->post('/document', 'DocumentController@registerDocument');

    // Matches "/api/visit
    $router->get('visit', 'VisitController@allVisit');

    // Matches "/api/visit/id
    $router->get('visit/{id}', 'VisitController@oneVisit');

    // Matches "/api/v1/visit
    $router->post('/vist', 'VisitController@registerVisit');

    // Matches "/api/v1/visit/id
    $router->put('visit/{id}', 'VisitController@updateAll');

    // Matches "/api/v1/visit/id
    $router->patch('visit/{id}', 'VisitController@update');

    // Matches "/api/v1/visit/id
    $router->delete('visit/{id}', 'VisitController@delete');
});
