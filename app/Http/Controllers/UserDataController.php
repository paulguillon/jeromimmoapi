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
     *   path="/api/v1/user/{id}/userdata/{keyUserData}",
     *   summary="Return data of a user",
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
     *  @OA\Parameter(
     *     name="keyUserData",
     *     in="path",
     *     required=true,
     *     description="Key of the data to get",
     *     @OA\Schema(
     *       type="string", default="key"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="One user data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idUserData",
     *         default="1",
     *         description="Id of the data of the user",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of creator",
     *       ),
     *       @OA\Property(
     *         property="keyUserData",
     *         default="Piscine",
     *         description="key",
     *       ),
     *       @OA\Property(
     *         property="valueUserData",
     *         default="true",
     *         description="value",
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default="1",
     *         description="Id of the user",
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

    public function getUserData($id, $keyUserData)
    {
        try {
            $userData = UserData::all()
                ->where('idUser', $id)
                ->where('keyUserData', $keyUserData)
                ->first();

            if (empty($userData))
                return response()->json(['message' => "Data of User  $id doesn't exist", 'status' => 'fail'], 500);

            return response()->json($userData, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data not found!' . $e->getMessage()], 404);
        }
    }
    /**
     * @OA\Post(
     *   path="/api/v1/user/{id}/userdata",
     *   summary="Add data of user",
     *   tags={"UserData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="keyUserData",
     *     in="query",
     *     required=true,
     *     description="Key of the data to add",
     *     @OA\Schema(
     *       type="string", default="One key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueUserData",
     *     in="query",
     *     required=true,
     *     description="Value of the data to add",
     *     @OA\Schema(
     *       type="any", default="One value"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="Id of the creator",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="Id of the user",
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
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of the creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of the creator",
     *       ),
     *       @OA\Property(
     *         property="keyUserData",
     *         default="Piscine",
     *         description="Key",
     *       ),
     *       @OA\Property(
     *         property="valueUserData",
     *         default="true",
     *         description="Value",
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default="1",
     *         description="Id of the user",
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
     *       response=500,
     *       description="User data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="User data not added",
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
    public function addData($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
            'keyUserData' => 'required|string',
            'valueUserData' => 'required|string'
        ]);

        try {
            $userData = new UserData;
            $userData->created_by = $request->input('created_by');
            $userData->updated_by = $request->input('updated_by');
            $userData->keyUserData = $request->input('keyUserData');
            $userData->valueUserData = $request->input('valueUserData');
            $userData->idUser = $id;
            $userData->save();

            // Return successful response
            return response()->json(['userData' => $userData, 'message' => 'CREATED', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'FAILED', 'status' => 'fail'], 409);
        }
    }
    /**
     * @OA\Patch(
     *   path="/api/v1/user/{id}/userdata/{keyUserData}",
     *   summary="Update data of an user",
     *   tags={"UserData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Id from user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyUserData",
     *     in="path",
     *     required=true,
     *     description="Key of the data to update",
     *     @OA\Schema(
     *       type="string", default="key"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User data updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idUserData",
     *         default="1",
     *         description="Id of the data of the user",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of creator",
     *       ),
     *       @OA\Property(
     *         property="keyUserData",
     *         default="Piscine",
     *         description="Key",
     *       ),
     *       @OA\Property(
     *         property="valueUserData",
     *         default="true",
     *         description="Value",
     *     
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default="1",
     *         description="Id of the user",
     *       ),
     *     )
     *),
     *   @OA\Response(
     *       response=409,
     *       description="Not updated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="User data not updated",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="User data not updated",
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
    public function updateData($id, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'keyUserData' => 'string',
            'valueUserData' => 'string',
            'idUser' => 'integer'
        ]);

        try {
            // Update
            $userData = User::findOrFail($id);
            if ($request->input('created_by') !== null)
                $userData->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $userData->updated_by = $request->input('updated_by');
            if ($request->input('keyUserData') !== null)
                $userData->keyUserData = $request->input('keyUserData');
            if ($request->input('valueUserData') !== null)
                $userData->valueUserData = $request->input('valueUserData');
            if ($request->input('idUser') !== null)
                $userData->idUser = $request->input('idUser');
            $userData->update();

            //return successful response
            return response()->json(['userData' => $userData, 'message' => 'UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'UserData update fail' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }


    /**
     * @OA\Delete(
     *   path="/api/v1/userdata/{id}",
     *   summary="Delete a user",
     *   tags={"UserData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the user to delete",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Not deleted",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="User data not deleted"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idUser",
     *         default="1",
     *         description="id of the user",
     *       ),
     *       @OA\Property(
     *         property="lastnameUser",
     *         default="lastname",
     *         description="Last name of the user",
     *       ),
     *       @OA\Property(
     *         property="firstnameUser",
     *         default="firstname",
     *         description="First name of the user",
     *       ),
     *       @OA\Property(
     *         property="emailUser",
     *         default="test@test.fr",
     *         description="Email address of the user",
     *       ),
     *       @OA\Property(
     *         property="passwordUser",
     *         default="1234",
     *         description="Password of the user",
     *       ),
     *       @OA\Property(
     *         property="idRoleUser",
     *         default="1",
     *         description="Id of the user's role",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default="[]",
     *         description="User data",
     *       ),
     *     )
     *   ),
     * )
     */
    public function deleteUserData($id)
    {
        try {
            $userData = UserData::findOrFail($id);

            $userData->delete();

            return response()->json(['userData' => $userData, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'User data deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function deleteData($idUser)
    {
        try {
            $userData = UserData::all()->where('idUser', $idUser);

            foreach ($userData as $data) {
                $data->delete();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function guard()
    {
        return Auth::guard();
    }
}
