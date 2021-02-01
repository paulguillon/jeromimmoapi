<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;

class UserController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods without authorization
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

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
            'idRoleUser' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {

            $user = new User;
            $user->uuidUser = Str::uuid();
            $user->lastnameUser = $request->input('lastnameUser');
            $user->firstnameUser = $request->input('firstnameUser');
            $user->emailUser = $request->input('emailUser');
            $plainPassword = $request->input('passwordUser');
            $user->passwordUser = app('hash')->make($plainPassword);
            $user->idRoleUser = $request->input('idRoleUser');
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
     * Update user
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function updateAll($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'lastnameUser' => 'required|string',
            'firstnameUser' => 'required|string',
            'emailUser' => 'required|email|unique:users,emailUser,' . $request->id . ',uuidUser',
            'passwordUser' => 'required|confirmed',
            'idRoleUser' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {
            $user = User::findOrFail($id);
            $user->lastnameUser = $request->input('lastnameUser');
            $user->firstnameUser = $request->input('firstnameUser');
            $user->emailUser = $request->input('emailUser');
            $plainPassword = $request->input('passwordUser');
            $user->passwordUser = app('hash')->make($plainPassword);
            $user->idRoleUser = $request->input('idRoleUser');
            $user->created_by = $request->input('created_by');
            $user->updated_by = $request->input('updated_by');

            $user->update();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update user patch.
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'lastnameUser' => 'string',
            'firstnameUser' => 'string',
            'emailUser' => 'email|unique:users,emailUser,' . $request->id . ',uuidUser',
            'passwordUser' => 'confirmed',
            'idRoleUser' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ]);

        try {
            $user = User::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);

            if ($request->input('lastnameUser') !== null)
                $user->lastnameUser = $request->input('lastnameUser');
            if ($request->input('firstnameUser') !== null)
                $user->firstnameUser = $request->input('firstnameUser');
            if ($request->input('emailUser') !== null)
                $user->emailUser = $request->input('emailUser');
            if ($request->input('passwordUser') !== null) {
                $plainPassword = $request->input('passwordUser');
                $user->passwordUser = app('hash')->make($plainPassword);
            }
            if ($request->input('idRoleUser') !== null)
                $user->idRoleUser = $request->input('idRoleUser');
            if ($request->input('created_by') !== null)
                $user->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $user->updated_by = $request->input('updated_by');

            $user->update();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
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
            $user = User::all()->where('uuidUser', $id)->first();

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
            return response()->json(['message' => 'Unauthorized', 'status' => 'failed'], 401);
        }
        return $this->respondWithToken($token);
    }

    public function logout()
    {
        $this->guard()->logout();
    }

    public function guard()
    {
        return Auth::guard();
    }
}
