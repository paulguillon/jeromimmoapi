<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get all users
     *
     * @param  Request  $request
     * @return Response
     */
    public function allUsers(Request $request)
    {
        return response()->json(['users' =>  User::all()], 200);
    }

    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'lastnameUser' => 'required|string',
            'firstnameUser' => 'required|string',
            'emailUser' => 'required|email|unique:users',
            'passwordUser' => 'required|confirmed',
            'idRole' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {

            $user = new User;
            $user->lastnameUser = $request->input('lastnameUser');
            $user->firstnameUser = $request->input('firstnameUser');
            $user->emailUser = $request->input('emailUser');
            $plainPassword = $request->input('passwordUser');
            $user->passwordUser = app('hash')->make($plainPassword);
            $user->idRole = $request->input('idRole');
            $user->created_by = $request->input('created_by');
            $user->updated_by = $request->input('updated_by');

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!' . $e->getMessage()], 409);
        }
    }
    /**
     * Get all users
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneUser($id)
    {
        try {
            $user = User::all()->where('idUser', $id)->first();

            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'user not found!' . $e->getMessage()], 404);
        }
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'emailUser' => 'required|string',
            'passwordUser' => 'required|string',
        ]);

        $credentials = ['emailUser' => $request->emailUser, 'password' => $request->passwordUser];


        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }
}
