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
    | Users Routes LOG       |
    |------------------------|
    */
    // Matches "/api/v1/login
    $router->post('login', 'UserController@login');
    // Matches "/api/v1/logout
    $router->post('logout', 'UserController@logout');
    /*
    |---------------------------|
    | Users Routes functions    |
    |---------------------------|
    */
    // Prefix
    $router->group(['prefix' => 'users'], function () use ($router) {
        // Matches "/api/v1/users Get all
        $router->get('', 'UserController@getUsers');
        // Matches "/api/v1/users/id Get one
        $router->get('/{id}', 'UserController@getUser');
        // Matches "/api/v1/users Post Register
        $router->post('', 'UserController@addUser');
        // Matches "/api/v1/users/data/idUser Post Register
        $router->post('/data/{id}', 'UserController@addData');
        // Matches "/api/v1/users/id update user
        $router->patch('/{id}', 'UserController@updateUser');
        // Matches "/api/v1/users/id Delete one User
        $router->delete('/{id}', 'UserController@deleteUser');
    });
    /*
    |------------------------|
    | Roles Routes           |
    |------------------------|
    */
    // Prefix
    $router->group(['prefix' => 'roles'], function () use ($router) {
        // Matches "/api/v1/roles/ Get all roles
        $router->get('', 'RoleController@getRoles');
        // Matches "/api/v1/roles/{id} Get One role
        $router->get('/{id}', 'RoleController@getRole');
        // Matches "/api/v1/roles Post Register role
        $router->post('', 'RoleController@addRole');
        // Matches "/api/v1/roles/id Patch one element of one role
        $router->patch('/{id}', 'RoleController@updateRole');
        // Matches "/api/v1/roles/id Delete one role
        $router->delete('/{id}', 'RoleController@deleteRole');
    });
    /*
    |------------------------|
    | Properties Routes      |
    |------------------------|
    */
    // Prefix
    $router->group(['prefix' => 'properties'], function () use ($router) {
        // Matches "/api/v1/properties Get all Properties
        $router->get('', 'PropertyController@getProperties');
        // Matches "/api/v1/properties/id Get one property
        $router->get('/{id}', 'PropertyController@getProperty');
        // Matches "/api/v1/properties Post register property
        $router->post('', 'PropertyController@addProperty');
        // Matches "/api/v1/properties/data/idProperty Post Register
        $router->post('/data/{id}', 'PropertyController@addData');
        // Matches "/api/v1/properties/id Patch one element of one Property
        $router->patch('/{id}', 'PropertyController@updateProperty');
        // Matches "/api/v1/properties/id Delete one Property
        $router->delete('/{id}', 'PropertyController@deleteProperty');
    });
    /*
    |------------------------|
    | Agency Routes          |
    |------------------------|
    */
    // Prefix
    $router->group(['prefix' => 'agency'], function () use ($router) {
        // Matches "/api/v1/agency Get All Agencies
        $router->get('', 'AgencyController@getAgencies');
        // Matches "/api/v1/agency/api/v1/id/ Get one agency
        $router->get('/{id}', 'AgencyController@getAgency');
        // Matches "/api/v1/agency Post register agency
        $router->post('', 'AgencyController@addAgency');
        // Matches "/api/v1/agency/data/idAgency Post Register
        $router->post('/data/{id}', 'AgencyController@addData');
        // Matches "/api/v1/agency/id Patch one element of one agency
        $router->patch('/{id}', 'AgencyController@updateAgency');
        // Matches "/api/v1/agency/id Delete one agency
        $router->delete('/{id}', 'AgencyController@deleteAgency');
    });
    /*
    |-----------------------|
    | FAQ Routes            |
    |-----------------------|
    */
    // Prefix
    $router->group(['prefix' => 'faq'], function () use ($router) {
        // Matches "/api/v1/faq Get all faq
        $router->get('', 'FaqController@getAllFaq');
        // Matches "/api/v1/faq/id Get one faq
        $router->get('/{id}', 'FaqController@getFaq');
        // Matches "/api/v1/faq Post add faq
        $router->post('', 'FaqController@addFaq');
        // Matches "/api/v1/faq/data/idFaq Post Register
        $router->post('/data/{id}', 'FaqController@addData');
        // Matches "/api/v1/faq/id Patch one element of one faq
        $router->patch('/{id}', 'FaqController@updateFaq');
        // Matches "/api/v1/faq/id delete one faq
        $router->delete('/{id}', 'FaqController@deleteFaq');
    });
    /*
    |-----------------------|
    | Document Routes       |
    |-----------------------|
    */
    // Prefix
    $router->group(['prefix' => 'document'], function () use ($router) {
        // Matches "/api/v1/document Get all Document
        $router->get('', 'DocumentController@getDocuments');
        // Matches "/api/v1/document/id Get one Document
        $router->get('/{id}', 'DocumentController@getDocument');
        // Matches "/api/v1/document Post register Document
        $router->post('', 'DocumentController@addDocument');
        // Matches "/api/v1/document/id Put all of one Document
        // $router->put('/{id}', 'DocumentController@put');
        // Matches "/api/v1/document/id Patch one element of one Document
        $router->patch('/{id}', 'DocumentController@updateDocument');
        // Matches "/api/v1/document/id Delete one Document
        $router->delete('/{id}', 'DocumentController@deleteDocument');
    });
    /*
    |-----------------------|
    | Visit Routes          |
    |-----------------------|
    */
    // Prefix
    $router->group(['prefix' => 'visit'], function () use ($router) {
        // Matches "/api/v1/visit Get all Visit
        $router->get('', 'VisitController@getVisits');
        // Matches "/api/v1/visit/id Get one Visit
        $router->get('/{id}', 'VisitController@getVisit');
        // Matches "/api/v1/visit Post register Visit
        $router->post('', 'VisitController@addVisit');
        // Matches "/api/v1/users/data/idVisit Post Register
        $router->post('/data/{id}', 'VisitController@addData');
        // Matches "/api/v1/visit/id Patch one element of one Visit
        $router->patch('/{id}', 'VisitController@updateVisit');
        // Matches "/api/v1/visit/id Delete one Visit
        $router->delete('/{id}', 'VisitController@deleteVisit');
    });
});
