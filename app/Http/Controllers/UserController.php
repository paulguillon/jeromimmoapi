<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserData;
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
    public function getUsers(Request $request)
    {
        return response()->json(['users' =>  User::all()], 200);
    }
    /**
     * Get one user
     *
     * @param  Request  $request
     * @return Response
     */
    public function getUser($id)
    {
        try {
            $user = User::all()->where('idUser', $id)->first();
            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'User not found!' . $e->getMessage()], 404);
        }
    }
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function addUser(Request $request)
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
            'data' => 'string',
        ]);

        try {
            $user = new User;
            $user->lastnameUser = $request->input('lastnameUser');
            $user->firstnameUser = $request->input('firstnameUser');
            $user->emailUser = $request->input('emailUser');
            $plainPassword = $request->input('passwordUser');
            $user->passwordUser = app('hash')->make($plainPassword);
            $user->idRoleUser = $request->input('idRoleUser');
            $user->created_by = $request->input('created_by');
            $user->updated_by = $request->input('updated_by');

            $user->save();

            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->addData($user->idUser, $key, $value, $request))
                        return response()->json(['message' => 'User data not added!', 'status' => 'fail'], 500);
                }
            }

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * Put user
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function updateUser($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'lastnameUser' => 'string',
            'firstnameUser' => 'string',
            'emailUser' => 'email|unique:users,emailUser,' . $request->id . ',idUser',
            'passwordUser' => 'confirmed',
            'idRoleUser' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',

            'data' => 'string',
        ]);

        try {
            // On modifie les infos principales du user
            $user = User::findOrFail($id);
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

            //maj des data
            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->updateData($user->idUser, $key, $value))
                        return response()->json(['message' => 'User Update Failed!', 'status' => 'fail'], 500);
                }
            }

            //return successful response
            return response()->json(['user' => $user, 'data' => $this->getAllData($user->idUser)->original, 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * Delete user function
     *
     * @param int $id
     * @return Response
     */
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $userData = UserData::all()->where('idUser', $id);

            //maj des data
            if ($userData !== null) {
                foreach ($userData as $key => $value) {
                    if (!$this->deleteData($user->idUser, $key))
                        return response()->json(['message' => 'User Deletion Failed!', 'status' => 'fail'], 500);
                }
            }

            $user->delete();

            return response()->json(['user' => $user, 'data' => $userData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function addData($idUser, $key, $value, $request)
    {
        try {
            $userData = new UserData;
            $userData->keyUserData = $key;
            $userData->valueUserData = $value;
            $userData->created_by = $request->input('created_by');
            $userData->updated_by = $request->input('updated_by');
            $userData->idUser = $idUser;

            $userData->save();

            //return successful response
            return response()->json(['user' => $userData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User data not added!' . $e->getMessage()], 409);
        }
    }

    public function getAllData($idUser)
    {
        return response()->json(UserData::all()->where('idUser', $idUser), 200);
    }

    public function getData($idUser, $key)
    {
        return response()->json(UserData::all()->where('idUser', $idUser)->where('keyUserData', $key), 200);
    }

    public function updateData($idUser, $key, $value)
    {
        try {
            $userData = UserData::all()->where('idUser', $idUser)->where('keyUserData', $key)->first();

            if ($userData == null)
                return false;

            $userData->valueUserData = $value;
            $userData->update();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteData($idUser, $key)
    {
        try {
            $userData = UserData::all()->where('idUser', $idUser)->where('keyUserData', $key)->first();

            if ($userData == null)
                return false;

            $userData->delete();

            return true;
        } catch (\Exception $e) {
            return false;
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
