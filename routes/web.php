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

    // Matches "/api/v1/role
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
    // Matches "/api/v1/users/id
    $router->get('users/{id}', 'UserController@oneUser');
    // Matches "/api/v1/users/id
    $router->put('users/{id}', 'UserController@updateAll');
    // Matches "/api/v1/users/id
    $router->patch('users/{id}', 'UserController@update');
    // Matches "/api/v1/users/id
    $router->delete('users/{id}', 'UserController@delete');

    // Matches "/api/v1/usersData
    $router->post('usersData', 'UserDataController@registerUserData');
    // Matches "/api/v1/usersData/{id}
    $router->get('usersData/{id}', 'UserDataController@oneUserData');
    // Matches "/api/v1/usersData/{id}
    $router->get('usersData', 'UserDataController@allUsersData');
    // Matches "/api/v1/usersData/id
    $router->put('usersData/{id}', 'UserDataController@put');
    // Matches "/api/v1/usersData/id
    $router->patch('usersData/{id}', 'UserDataController@patch');
    // Matches "/api/v1/usersData/id
    $router->delete('usersData/{id}', 'UserDataController@delete');

    // Matches "/api/v1/login
    $router->post('login', 'UserController@login');
    // Matches "/api/v1/logout
    $router->post('logout', 'UserController@logout');
    // Matches "/api/v1/register
    $router->post('register', 'UserController@register');

    // Matches "/api/properties
    $router->get('properties', 'PropertyController@allProperties');
    // Matches "/api/properties/id
    $router->get('properties/{id}', 'PropertyController@oneProperty');
    // Matches "/api/v1/properties/id
    $router->put('properties/{id}', 'PropertyController@updateAll');
    // Matches "/api/v1/properties/id
    $router->patch('properties/{id}', 'PropertyController@update');
    // Matches "/api/v1/properties/id
    $router->delete('properties/{id}', 'PropertyController@delete');
    // Matches "/api/v1/registerProperty
    $router->post('/registerProperty', 'PropertyController@registerProperty');

    // Matches "/api/propertiesData
    $router->get('propertiesData', 'PropertyDataController@allPropertiesData');
    // Matches "/api/propertiesData/id
    $router->get('propertiesData/{id}', 'PropertyDataController@onePropertyData');
    // Matches "/api/v1/propertiesData/id
    $router->put('propertiesData/{id}', 'PropertyDataController@updateAll');
    // Matches "/api/v1/propertiesData/id
    $router->patch('propertiesData/{id}', 'PropertyDataController@update');
    // Matches "/api/v1/propertiesData/id
    $router->delete('propertiesData/{id}', 'PropertyDataController@delete');
    // Matches "/api/v1/registerPropertyData
    $router->post('/registerPropertyData', 'PropertyDataController@registerPropertyData');

    // Matches "/api/agency
    $router->get('/agency', 'AgencyController@allAgency');
    // Matches "/api/agency/id
    $router->get('agency/{id}', 'AgencyController@oneAgency');
    // Matches "/api/agency/id
    $router->put('agency/{id}', 'AgencyController@put');
    // Matches "/api/agency/id
    $router->patch('agency/{id}', 'AgencyController@patch');
    // Matches "/api/v1/registerAgency
    $router->post('/registerAgency', 'AgencyController@registerAgency');

    // Matches "/api/agencyData
    $router->get('/agencyData', 'AgencyDataController@allAgencyData');
    // Matches "/api/agencyData/id
    $router->get('agencyData/{id}', 'AgencyDataController@oneAgencyData');
    // Matches "/api/agencyData/id
    $router->put('agencyData/{id}', 'AgencyDataController@put');
    // Matches "/api/agencyData/id
    $router->patch('agencyData/{id}', 'AgencyDataController@patch');
    // Matches "/api/v1/registerAgencyData
    $router->post('/registerAgencyData', 'AgencyDataController@registerAgencyData');

    // Matches "/api/faq
    $router->get('faq', 'FaqController@allFaq');
    // Matches "/api/agency/id
    $router->get('faq/{id}', 'FaqController@oneFaq');
    // Matches "/api/v1/registerFaq
    $router->post('/registerFaq', 'FaqController@registerFaq');
    // Matches "/api/v1/faq/id
    $router->put('faq/{id}', 'FaqController@updateAll');
    // Matches "/api/v1/faq/id
    $router->patch('faq/{id}', 'FaqController@update');
    // Matches "/api/v1/faq/id
    $router->delete('faq/{id}', 'FaqController@delete');

    // Matches "/api/faqData
    $router->get('faqData', 'FaqDataController@allFaq');
    // Matches "/api/faqData/id
    $router->get('faqData/{id}', 'FaqDataController@oneFaq');
    // Matches "/api/v1/registerFaqData
    $router->post('/registerFaqData', 'FaqDataController@registerFaq');
    // Matches "/api/v1/faqData/id
    $router->put('faqData/{id}', 'FaqDataController@updateAll');
    // Matches "/api/v1/faqData/id
    $router->patch('faqData/{id}', 'FaqDataController@update');
    // Matches "/api/v1/faqData/id
    $router->delete('faqData/{id}', 'FaqDataController@delete');

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
    // Matches "/api/v1/registerDocument
    $router->post('/registerDocument', 'DocumentController@registerDocument');

    // Matches "/api/documentData
    $router->get('documentData', 'DocumentDataController@allDocumentData');
    // Matches "/api/documentData/id
    $router->get('documentData/{id}', 'DocumentDataController@oneDocumentData');
    // Matches "/api/v1/documentData/id
    $router->put('documentData/{id}', 'DocumentDataController@updateAll');
    // Matches "/api/v1/documentData/id
    $router->patch('documentData/{id}', 'DocumentDataController@update');
    // Matches "/api/v1/documentData/id
    $router->delete('documentData/{id}', 'DocumentDataController@delete');
    // Matches "/api/v1/registerDocumentData
    $router->post('/registerDocumentData', 'DocumentDataController@registerDocumentData');

    // Matches "/api/visit
    $router->get('visit', 'VisitController@allVisit');
    // Matches "/api/visit/id
    $router->get('visit/{id}', 'VisitController@oneVisit');
    // Matches "/api/v1/registerVisit
    $router->post('/registerVisit', 'VisitController@registerVisit');
    // Matches "/api/v1/visit/id
    $router->put('visit/{id}', 'VisitController@updateAll');
    // Matches "/api/v1/visit/id
    $router->patch('visit/{id}', 'VisitController@update');
    // Matches "/api/v1/visit/id
    $router->delete('visit/{id}', 'VisitController@delete');

    // Matches "/api/visitData
    $router->get('visitData', 'VisitDataController@allVisitData');
    // Matches "/api/visitData/id
    $router->get('visitData/{id}', 'VisitDataController@oneVisitData');
    // Matches "/api/v1/visitData
    $router->post('/visitData', 'VisitDataController@registerVisitData');
    // Matches "/api/v1/visitData/id
    $router->put('visitData/{id}', 'VisitDataController@updateAll');
    // Matches "/api/v1/visitData/id
    $router->patch('visitData/{id}', 'VisitDataController@update');
    // Matches "/api/v1/visitData/id
    $router->delete('visitData/{id}', 'VisitDataController@delete');
});
