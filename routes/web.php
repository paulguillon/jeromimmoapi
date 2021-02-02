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

/*
|------------------------|
| Users Routes            |
|------------------------|
*/
    // Matches "/api/v1/login
    $router->post('login', 'UserController@login');
    // Matches "/api/v1/register
    $router->post('register', 'UserController@register');
    // Matches "/api/v1/logout
    $router->post('logout', 'UserController@logout');

/*
|------------------------|
| Roles Routes           |
|------------------------|
*/
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

    //Routes usersData
    $router->group(['prefix' => 'usersData'], function () use ($router) {
        // Matches "/api/v1/usersData
        $router->post('/', 'UserDataController@registerUserData');
        // Matches "/api/v1/usersData
        $router->get('/', 'UserDataController@allUsersData');
        // Matches "/api/v1/usersData/id Get One
        $router->get('/{id}', 'UserDataController@oneUserData');
        // Matches "/api/v1/usersData/id Put all of One User
        $router->put('/{id}', 'UserDataController@put');
        // Matches "/api/v1/usersData/id
        $router->patch('/{id}', 'UserDataController@patch');
        // Matches "/api/v1/usersData/id
        $router->delete('/{id}', 'UserDataController@delete');
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

    //Routes propertiesData
    $router->group(['prefix' => 'propertiesData'], function () use ($router) {
        // Matches "/api/propertiesData
        $router->get('/', 'PropertyDataController@allPropertiesData');
        // Matches "/api/propertiesData/id
        $router->get('/{id}', 'PropertyDataController@onePropertyData');
        // Matches "/api/v1/propertiesData
        $router->post('/', 'PropertyDataController@registerPropertyData');
        // Matches "/api/v1/propertiesData/id
        $router->put('/{id}', 'PropertyDataController@put');
        // Matches "/api/v1/propertiesData/id
        $router->patch('/{id}', 'PropertyDataController@patch');
        // Matches "/api/v1/propertiesData/id
        $router->delete('/{id}', 'PropertyDataController@delete');
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

    //Routes agencyData
    $router->group(['prefix' => 'agencyData'], function () use ($router) {
        // Matches "/api/agencyData
        $router->get('/', 'AgencyDataController@allAgencyData');
        // Matches "/api/agencyData/id
        $router->get('/{id}', 'AgencyDataController@oneAgencyData');
        // Matches "/api/v1/agencyData
        $router->post('/', 'AgencyDataController@registerAgencyData');
        // Matches "/api/agencyData/id
        $router->put('/{id}', 'AgencyDataController@put');
        // Matches "/api/agencyData/id
        $router->patch('/{id}', 'AgencyDataController@patch');
        // Matches "/api/v1/agencyData/id
        $router->delete('/{id}', 'AgencyDataController@delete');
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
        $router->put('/{id}', 'FaqController@updateAll');
        // Matches "/api/v1/faq/id
        $router->patch('/{id}', 'FaqController@update');
        // Matches "/api/v1/faq/id
        $router->delete('/{id}', 'FaqController@delete');
    });

    //Routes FAQData
    $router->group(['prefix' => 'faqData'], function () use ($router) {
        // Matches "/api/faqData
        $router->get('/', 'FaqDataController@allFaqData');
        // Matches "/api/faqData/id
        $router->get('/{id}', 'FaqDataController@oneFaqData');
        // Matches "/api/v1/faqData
        $router->post('/', 'FaqDataController@registerFaqData');
        // Matches "/api/v1/faqData/id
        $router->put('/{id}', 'FaqDataController@updateAll');
        // Matches "/api/v1/faqData/id
        $router->patch('/{id}', 'FaqDataController@update');
        // Matches "/api/v1/faqData/id
        $router->delete('/{id}', 'FaqDataController@delete');
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

    //Routes documentData
    $router->group(['prefix' => 'documentData'], function () use ($router) {
        // Matches "/api/documentData
        $router->get('/', 'DocumentDataController@allDocumentData');
        // Matches "/api/documentData/id
        $router->get('/{id}', 'DocumentDataController@oneDocumentData');
        // Matches "/api/v1/documentData
        $router->post('/', 'DocumentDataController@registerDocumentData');
        // Matches "/api/v1/documentData/id
        $router->put('/{id}', 'DocumentDataController@updateAll');
        // Matches "/api/v1/documentData/id
        $router->patch('/{id}', 'DocumentDataController@update');
        // Matches "/api/v1/documentData/id
        $router->delete('/{id}', 'DocumentDataController@delete');
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

    //Routes visitData
    $router->group(['prefix' => 'visitData'], function () use ($router) {
        // Matches "/api/visitData
        $router->get('/', 'VisitDataController@allVisitData');
        // Matches "/api/visitData/id
        $router->get('/{id}', 'VisitDataController@oneVisitData');
        // Matches "/api/v1/visitData
        $router->post('/', 'VisitDataController@registerVisitData');
        // Matches "/api/v1/visitData/id
        $router->put('/{id}', 'VisitDataController@updateAll');
        // Matches "/api/v1/visitData/id
        $router->patch('/{id}', 'VisitDataController@update');
        // Matches "/api/v1/visitData/id
        $router->delete('/{id}', 'VisitDataController@delete');
    });
});
