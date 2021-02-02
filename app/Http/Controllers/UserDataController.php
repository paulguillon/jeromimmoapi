<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;

class UserDataController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods without authorization
        $this->middleware('auth:api', ['except' => ['registerUserData']]);
    }

    /**
     * Get all users data
     *
     * @param  Request  $request
     * @return Response
     */
    public function allUsersData(Request $request)
    {
        return response()->json(['usersData' =>  UserData::all()], 200);
    }

    /**
     * Get one user data
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneUserData($id)
    {
        try {
            $userData = UserData::all()->where('idUserData', $id)->first();

            return response()->json(['userData' => $userData], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'user data not found!' . $e->getMessage()], 404);
        }
    }

    /**
     * Store a new user data.
     *
     * @param  Request  $request
     * @return Response
     */
    public function registerUserData(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyUserData' => 'required|string',
            'valueUserData' => 'required|string',
            'idUser' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {

            $userData = new UserData;
            $userData->keyUserData = $request->input('keyUserData');
            $userData->valueUserData = $request->input('valueUserData');
            $userData->idUser = $request->input('idUser');
            $userData->created_by = $request->input('created_by');
            $userData->updated_by = $request->input('updated_by');

            $userData->save();

            //return successful response
            return response()->json(['userData' => $userData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Data Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update user data
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function put($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyUserData' => 'required|string',
            'valueUserData' => 'required|string',
            'idUser' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $userData = UserData::findOrFail($id);
            $userData->keyUserData = $request->input('keyUserData');
            $userData->valueUserData = $request->input('valueUserData');
            $userData->idUser = $request->input('idUser');
            $userData->created_by = $request->input('created_by');
            $userData->updated_by = $request->input('updated_by');

            $userData->update();

            //return successful response
            return response()->json(['userData' => $userData, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Data Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update user patch.
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function patch($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyUserData' => 'required|string',
            'valueUserData' => 'required|string',
            'idUser' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $userData = UserData::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);
            if ($request->input('keyUserData') !== null)
                $userData->keyUserData = $request->input('keyUserData');
            if ($request->input('valueUserData') !== null)
                $userData->valueUserData = $request->input('valueUserData');
            if ($request->input('idUser') !== null)
                $userData->idUser = $request->input('idUser');
            if ($request->input('created_by') !== null)
                $userData->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $userData->updated_by = $request->input('updated_by');

            $userData->update();

            //return successful response
            return response()->json(['userData' => $userData, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User data Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function delete($id)
    {
        try {
            $userData = UserData::findOrFail($id);
            $userData->delete();

            return response()->json(['userData' => $userData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User data deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
