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
        // Matches "/api/v1/users/id update user
        $router->patch('/{id}', 'UserController@updateUser');
        // Matches "/api/v1/users/id Delete one User
        $router->delete('/{id}', 'UserController@deleteUser');
    });
    // /*
    // |------------------------|
    // | UserData Routes        |
    // |------------------------|
    // */
    // // Prefix
    // $router->group(['prefix' => 'userData'], function () use ($router) {
    //     // Matches "/api/v1/userData Get all
    //     $router->get('', 'UserDataController@allUsersData');
    //     // Matches "/api/v1/userData/id Get One
    //     $router->get('/{id}', 'UserDataController@oneUserData');
    //     // Matches "/api/v1/userData Register UsersData
    //     $router->post('', 'UserDataController@registerUserData');
    //     // Matches "/api/v1/userData/id Put all of One userData
    //     $router->put('/{id}', 'UserDataController@put');
    //     // Matches "/api/v1/userData/id Patch one element of one userData
    //     $router->patch('/{id}', 'UserDataController@patch');
    //     // Matches "/api/v1/userData/id Delete one userData
    //     $router->delete('/{id}', 'UserDataController@delete');
    // });
    /*
    |------------------------|
    | Roles Routes           |
    |------------------------|
    */
    // Prefix
    $router->group(['prefix' => 'roles'], function () use ($router) {
        // Matches "/api/v1/roles/ Get all roles
        $router->get('', 'RoleController@allRoles');
        // Matches "/api/v1/roles/{id} Get One role
        $router->get('/{id}', 'RoleController@oneUserRole');
        // Matches "/api/v1/roles Post Register role
        $router->post('', 'RoleController@createRole');
        // Matches "/api/v1/roles/id Put all of One role
        $router->put('/{id}', 'RoleController@put');
        // Matches "/api/v1/roles/id Patch one element of one role
        $router->patch('/{id}', 'RoleController@patch');
        // Matches "/api/v1/roles/id Delete one role
        $router->delete('/{id}', 'RoleController@delete');
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
        // Matches "/api/v1/properties/id Put all of One property
        // $router->put('/{id}', 'PropertyController@put');
        // Matches "/api/v1/properties/id Patch one element of one Property
        $router->patch('/{id}', 'PropertyController@updateProperty');
        // Matches "/api/v1/properties/id Delete one Property
        $router->delete('/{id}', 'PropertyController@deleteProporty');
    });
    // /*
    // |------------------------|
    // | PropertyData Routes    |
    // |------------------------|
    // */
    // // Prefix
    // $router->group(['prefix' => 'propertyData'], function () use ($router) {
    //     // Matches "/api/v1/propertyData Get All propertiesData
    //     $router->get('', 'PropertyDataController@allPropertiesData');
    //     // Matches "/api/v1/propertyData/id Get One PropertyData
    //     $router->get('/{id}', 'PropertyDataController@onePropertyData');
    //     // Matches "/api/v1/propertyData Register PropertyData
    //     $router->post('', 'PropertyDataController@registerPropertyData');
    //     // Matches "/api/v1/propertyData/id Put all of one propertyData
    //     $router->put('/{id}', 'PropertyDataController@put');
    //     // Matches "/api/v1/propertyData/id Patch one element of one propertyData
    //     $router->patch('/{id}', 'PropertyDataController@patch');
    //     // Matches "/api/v1/propertyData/id Delete one propertyData
    //     $router->delete('/{id}', 'PropertyDataController@delete');
    // });
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
        // Matches "/api/v1/agency/id Patch one element of one agency
        $router->patch('/{id}', 'AgencyController@updateAgency');
        // Matches "/api/v1/agency/id Delete one agency
        $router->delete('/{id}', 'AgencyController@deleteAgency');
    });
    /*
    |------------------------|
    | AgencyData Routes      |
    |------------------------|
    */
    // Prefix
    // $router->group(['prefix' => 'agencyData'], function () use ($router) {
    //     // Matches "/api/v1/agencyData Get All agencyData
    //     $router->get('', 'AgencyDataController@allAgencyData');
    //     // Matches "/api/v1/agencyData/id Get one agencyData
    //     $router->get('/{id}', 'AgencyDataController@oneAgencyData');
    //     // Matches "/api/v1/agencyData Post register agencyData
    //     $router->post('', 'AgencyDataController@registerAgencyData');
    //     // Matches "/api/v1/agencyData/id Put all of one agencyData
    //     $router->put('/{id}', 'AgencyDataController@put');
    //     // Matches "/api/v1/agencyData/id Patch one element of one agencyData
    //     $router->patch('/{id}', 'AgencyDataController@patch');
    //     // Matches "/api/v1/agencyData/id Delete one agencyData
    //     $router->delete('/{id}', 'AgencyDataController@delete');
    // });
    /*
    |-----------------------|
    | FAQ Routes            |
    |-----------------------|
    */
    // Prefix
    $router->group(['prefix' => 'faq'], function () use ($router) {
        // Matches "/api/v1/faq Get all faq
        $router->get('', 'FaqController@allFaq');
        // Matches "/api/v1/faq/id Get one faq
        $router->get('/{id}', 'FaqController@oneFaq');
        // Matches "/api/v1/faq Post register faq
        $router->post('', 'FaqController@registerFaq');
        // Matches "/api/v1/faq/id Put all of one faq
        $router->put('/{id}', 'FaqController@put');
        // Matches "/api/v1/faq/id Patch one element of one faq
        $router->patch('/{id}', 'FaqController@patch');
        // Matches "/api/v1/faq/id delete one faq
        $router->delete('/{id}', 'FaqController@delete');
    });
    /*
    |-----------------------|
    | faqData Routes        |
    |-----------------------|
    */
    // Prefix
    $router->group(['prefix' => 'faqData'], function () use ($router) {
        // Matches "/api/v1/faqData Get all faqData
        $router->get('', 'FaqDataController@allFaqData');
        // Matches "/api/v1/faqData/id Get one faqData
        $router->get('/{id}', 'FaqDataController@oneFaqData');
        // Matches "/api/v1/faqData Post register faqData
        $router->post('', 'FaqDataController@registerFaqData');
        // Matches "/api/v1/faqData/id Put all of one faqData
        $router->put('/{id}', 'FaqDataController@updateAll');
        // Matches "/api/v1/faqData/id Patch one element of one faqData
        $router->patch('/{id}', 'FaqDataController@update');
        // Matches "/api/v1/faqData/id Delete one faqData
        $router->delete('/{id}', 'FaqDataController@delete');
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
    | documentData Routes   |
    |-----------------------|
    */
    // Prefix
    $router->group(['prefix' => 'documentData'], function () use ($router) {
        // Matches "/api/v1/documentData Get all documentData
        $router->get('', 'DocumentDataController@allDocumentData');
        // Matches "/api/v1/documentData/id Get one documentData
        $router->get('/{id}', 'DocumentDataController@oneDocumentData');
        // Matches "/api/v1/documentData Post register documentData
        $router->post('', 'DocumentDataController@registerDocumentData');
        // Matches "/api/v1/documentData/id Put all of one documentData
        $router->put('/{id}', 'DocumentDataController@put');
        // Matches "/api/v1/documentData/id Patch one element of one documentData
        $router->patch('/{id}', 'DocumentDataController@patch');
        // Matches "/api/v1/documentData/id Delete documentData
        $router->delete('/{id}', 'DocumentDataController@delete');
    });
    /*
    |-----------------------|
    | Visit Routes          |
    |-----------------------|
    */
    // Prefix
    $router->group(['prefix' => 'visit'], function () use ($router) {
        // Matches "/api/v1/visit Get all Visit
        $router->get('', 'VisitController@allVisit');
        // Matches "/api/v1/visit/id Get one Visit
        $router->get('/{id}', 'VisitController@oneVisit');
        // Matches "/api/v1/visit Post register Visit
        $router->post('', 'VisitController@registerVisit');
        // Matches "/api/v1/visit/id Put all of one Visit
        $router->put('/{id}', 'VisitController@put');
        // Matches "/api/v1/visit/id Patch one element of one Visit
        $router->patch('/{id}', 'VisitController@patch');
        // Matches "/api/v1/visit/id Delete one Visit
        $router->delete('/{id}', 'VisitController@delete');
    });
    /*
    |-----------------------|
    | visitData Routes      |
    |-----------------------|
    */
    // Prefix
    $router->group(['prefix' => 'visitData'], function () use ($router) {
        // Matches "/api/v1/visitData Get all visitData
        $router->get('', 'VisitDataController@allVisitData');
        // Matches "/api/visitData/id Get one visitData
        $router->get('/{id}', 'VisitDataController@oneVisitData');
        // Matches "/api/v1/visitData Post register visitData
        $router->post('', 'VisitDataController@registerVisitData');
        // Matches "/api/v1/visitData/id Put all of one visitData
        $router->put('/{id}', 'VisitDataController@updateAll');
        // Matches "/api/v1/visitData/id Patch one element of one visitData
        $router->patch('/{id}', 'VisitDataController@update');
        // Matches "/api/v1/visitData/id Delete one visitData
        $router->delete('/{id}', 'VisitDataController@delete');
    });
});
