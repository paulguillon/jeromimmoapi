<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
//import auth facades
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     *   title="Jeromimmo API",
     *  version="1.0.0",
     *  @OA\Contact(
     *    email="developers@module.com",
     *    name="Developer Team"
     *  )
     * )
     */

    //Add this method to the Controller class
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'status' => 'success',
        ], 200);
    }
}

/**
 * @OA\Info(
 *   title="Your Awesome Modules's API",
 *  version="1.0.0",
 *  @OA\Contact(
 *    email="developers@module.com",
 *    name="Developer Team"
 *  )
 * )
 */
