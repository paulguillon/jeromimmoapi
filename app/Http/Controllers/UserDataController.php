<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\Auth;

class UserDataController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods without authorization
        $this->middleware('auth:api', []);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/users/{id}/data",
     *   summary="Return all data of specific user",
     *   tags={"UserData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Id from user",
     *     @OA\Schema(
     *       type="number", default=1
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idUserData",
     *         default="1",
     *         description="Id of the user data",
     *       ),
     *       @OA\Property(
     *         property="keyUserData",
     *         default="Any key",
     *         description="Key of the user data",
     *       ),
     *       @OA\Property(
     *         property="valueUserData",
     *         default="Any value",
     *         description="Value of the user data",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default="1",
     *         description="ID of the user that this data is related to",
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data could not be retrieved"
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="UserData not found",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="No data for this user",
     *          description="Message",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="fail",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     * )
     */

    public function getAllData($id)
    {
        try {
            //if user doesn't exists
            if (!$this->existUser($id)) return response()->json(['data' => null, 'message' => "User doesn't exists", 'status' => 'fail'], 404);

            $data = array_values(UserData::all()
                ->where('idUser', $id)->toArray());
            return response()->json(['total' => count($data), 'data' => $data, 'message' => 'User data successfully retrieved', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!', 'status' => 'fail'], 409);
        }
    }
    /**
     * @OA\Get(
     *   path="/api/v1/users/{id}/data/{key}",
     *   summary="Return specific data of the specified user",
     *   tags={"UserData Controller"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the concerned user",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="key of the user to get",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Requested data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idUserData",
     *         default="1",
     *         description="ID of the user",
     *       ),
     *       @OA\Property(
     *         property="keyUserData",
     *         default="key",
     *         description="Key of the user",
     *       ),
     *       @OA\Property(
     *         property="valueUserData",
     *         default="Any value",
     *         description="Value of the user",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Who updates",
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default="1",
     *         description="User associated with the data",
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No data for this key"
     *   ),
     *   @OA\Response(
     *     response=409,
     *     description="Server error"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Data not found",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         default="Data doesn't exist",
     *         description="Message",
     *       ),
     *       @OA\Property(
     *         property="status",
     *         default="fail",
     *         description="Status",
     *       ),
     *     ),
     *   ),
     * )
     */
    public function getUserData($id, $key)
    {
        try {
            //if property doesn't exists
            if (!$this->existUser($id))
                return response()->json(['data' => null, 'message' => "User doesn't exists", 'status' => 'fail'], 404);

            $userData = UserData::all()
                ->where('idUser', $id)
                ->where('keyUserData', $key)
                ->first();

            //key doesn't exists
            if (!$userData)
                return response()->json(['data' => null, 'message' => "No data for this key", 'status' => 'fail'], 404);

            return response()->json(['data' => $userData, 'message' => 'Data successfully retrieved!', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
    /**
     * @OA\Post(
     *   path="/api/v1/users/{id}/data",
     *   summary="Add a data to a specific user",
     *   tags={"UserData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the user",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyUserData",
     *     in="query",
     *     required=true,
     *     description="Key of the user data",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueUserData",
     *     in="query",
     *     required=true,
     *     description="Value of the user data",
     *     @OA\Schema(
     *       type="any", default="Any value"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the creator",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the user",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="idUser",
     *     in="query",
     *     required=true,
     *     description="Id from user",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="User data added",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idUserData",
     *         default="1",
     *         description="Id of the data of the user",
     *       ),
     *       @OA\Property(
     *         property="keyUserData",
     *         default="Some key",
     *         description="Key to add",
     *       ),
     *       @OA\Property(
     *         property="valueUserData",
     *         default="Any value",
     *         description="Value of the key to add",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="ID of user who has updated",
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default="1",
     *         description="User's ID who this new data is related to",
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     * ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *     response=409,
     *     description="Data addition failed!",
     *   ),
     * )
     */
    public function addUserData($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyUserData' => 'required|string',
            'valuePropertyData' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            //if  created_by and updated_by doesn't exist
            $created_by = User::all()->where('idUser', $request->input('created_by'))->first();
            $updated_by = User::all()->where('idUser', $request->input('updated_by'))->first();
            if (!$this->existUser($id))
                return response()->json(['data' => null, 'message' => "Unknown User", 'status' => 'fail'], 404);
            if (!$created_by)
                return response()->json(['data' => null, 'message' => "Creator unknown", 'status' => 'fail'], 404);
            if (!$updated_by)
                return response()->json(['data' => null, 'message' => "User unknown", 'status' => 'fail'], 404);

            //if property data already exists
            $exist = UserData::all()
                ->where('keyUserData', $request->input('keyUserData'))
                ->where('idUser', $id)
                ->first();
            if ($exist)
                return response()->json(['data' => null, 'message' => "Data already exists", 'status' => 'fail'], 404);

            //creation of the new data
            $userData = new UserData;
            $userData->keyUserData = $request->input('keyUserData');
            $userData->valueUserData = $request->input('valueUserData');
            $userData->created_by = (int)$request->input('created_by');
            $userData->updated_by = (int)$request->input('updated_by');
            $userData->idUser = (int)$id;
            $userData->save();

            // Return successful response
            return response()->json(['userData' => $userData, 'message' => 'User data successfully created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Data addition failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
    /**
     * @OA\Patch(
     *   path="/api/v1/users/{id}/data/{key}",
     *   summary="Update a user data",
     *   tags={"UserData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Key of the user related to the data to update",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the user data to update",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyUserData",
     *     in="query",
     *     required=false,
     *     description="New keyUserData",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueUserData",
     *     in="query",
     *     required=false,
     *     description="New valueUserData",
     *     @OA\Schema(
     *       type="string", default="any value"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=false,
     *     description="New creator",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=false,
     *     description="New user who updates",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="idUser",
     *     in="query",
     *     required=false,
     *     description="New idUser",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User data updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idUserData",
     *         default="1",
     *         description="Id of the data user",
     *       ),
     *       @OA\Property(
     *         property="keyUserData",
     *         default="thumbnail",
     *         description="Key of the user data",
     *       ),
     *       @OA\Property(
     *         property="valueUserData",
     *         default="any value",
     *         description="Value of the user data",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of creator",
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default="1",
     *         description="ID of user this data is related to",
     *       ),
     *     )
     *),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data update failed",
     *   ),
     * )
     */
    public function updateUserData($id, $key, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'keyUserData' => 'string',
            'valueUserData' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'idUser' => 'integer'
        ]);

        try {
            //if created_by and updated_by doesn't exist
            if ($request->input('created_by')) {
                $created_by = User::all()->where('idUser', $request->input('created_by'))->first();
                if (empty($created_by))
                    return response()->json(['data' => null, 'message' => "Creator unknown", 'status' => 'fail'], 404);
            }
            if ($request->input('updated_by')) {
                $updated_by = User::all()->where('idUser', $request->input('updated_by'))->first();
                if (empty($updated_by))
                    return response()->json(['data' => null, 'message' => "User unknown", 'status' => 'fail'], 404);
            }

            //if user doesn't exist
            if (!$this->existUser($id))
                return response()->json(['data' => null, 'message' => "Unknown Property", 'status' => 'fail'], 404);

            //test if the new key already exists
            $newKeyExist = UserData::all()
                ->where('idUser', $id)
                ->where('keyUserData', $request->input('keyUserData'))
                ->first();
            if ($newKeyExist)
                return response()->json(['message' => 'Data with this key already exists', 'status' => 'fail'], 404);

            // update
            $userData = UserData::all()
                ->where('idUser', $id)
                ->where('keyUserData', $key)
                ->first();
            if (!$userData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            if ($request->input('keyUserData') !== null)
                $userData->keyUserData = $request->input('keyUserData');
            if ($request->input('valueUserData') !== null)
                $userData->valueUserData = $request->input('valueUserData');
            if ($request->input('created_by') !== null)
                $userData->created_by = (int)$request->input('created_by');
            if ($request->input('updated_by') !== null)
                $userData->updated_by = (int)$request->input('updated_by');
            if ($request->input('idProperty') !== null)
                $userData->idProperty = (int)$request->input('idProperty');

            $userData->update();

            // Return successful response
            return response()->json(['data' => $userData, 'message' => 'Data successfully updated', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'User data Update Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/users/{id}/data/{key}",
     *   summary="Delete a user data",
     *   tags={"UserData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the user data to delete",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the user data to delete",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User data deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="keyUserData",
     *         default="Any key",
     *         description="Key of the user data",
     *       ),
     *       @OA\Property(
     *         property="valueUserData",
     *         default="Any value",
     *         description="Value of the user data",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="ID of user who deleted this data",
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default=1,
     *         description="ID of user this data was related to",
     *       )
     *      )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="No data for this key"
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data deletion failed!",
     *   ),
     * )
     */
    public function deleteUserData($id, $key)
    {
        try {
            $userData = UserData::all()
                ->where('idUser', $id)
                ->where('keyUserData', $key)
                ->first();

            if (!$userData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            $userData->delete();

            return response()->json(['data' => $userData, 'message' => 'Data successfully deleted', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Data deletion failed!', 'status' => 'fail'], 409);
        }
    }

    public function guard()
    {
        return Auth::guard();
    }


    private function existUser($id)
    {
        $user = User::all()
            ->where('idUser', $id)
            ->first();
        return (bool) $user;
    }
}
