<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
//import auth facades
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *   title="Your Awesome Modules's API",
 *  version="1.0.0",
 *  @OA\Contact(
 *    email="developers@module.com",
 *    name="Developer Team"
 *  )
 * ),
 * @OA\Parameter(
 *   parameter="get_request_parameter_limit",
 *   name="limit",
 *   description="Limit the number of results",
 *   in="query",
 *   @OA\Schema(
 *     type="number", default=10
 *   )
 * ),
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token based Based",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="apiAuth",
 * )
 */
class Controller extends BaseController
{

    //Add this method to the Controller class
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL(),
            'status' => 'success',
        ], 200);
    }
}
