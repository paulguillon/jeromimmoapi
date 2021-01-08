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
$router->get('/logout', function () use ($router) {
    return view('logout');
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

    // Matches "/api/agency
    $router->get('/agency', 'AgencyController@allAgency');

    // Matches "/api/agency/id
    $router->get('agency/{id}', 'AgencyController@oneAgency');

    // Matches "/api/v1/registerAgency
    $router->post('/registerAgency', 'AgencyController@registerAgency');

    // Matches "/api/faq
    $router->get('faq', 'FaqController@allFaq');

    // Matches "/api/agency/id
    $router->get('faq/{id}', 'FaqController@oneFaq');

    // Matches "/api/v1/registerFaq
    $router->post('/registerFaq', 'FaqController@registerFaq');

    // Matches "/api/document
    $router->get('document', 'DocumentController@allDocument');

    // Matches "/api/document/id
    $router->get('document/{id}', 'DocumentController@oneDocument');

    // Matches "/api/v1/registerDocument
    $router->post('/registerDocument', 'DocumentController@registerDocument');

    // Matches "/api/visit
    $router->get('visit', 'VisitController@allVisit');

    // Matches "/api/visit/id
    $router->get('visit/{id}', 'VisitController@oneVisit');

    // Matches "/api/v1/registerVisit
    $router->post('/registerVisit', 'VisitController@registerVisit');
});
