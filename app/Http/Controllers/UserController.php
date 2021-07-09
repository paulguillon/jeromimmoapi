<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods without authorization
        $this->middleware('auth:api', ['except' => ['login', 'addUser']]);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/login",
     *   summary="Log in",
     *   tags={"Login"},
     *     @OA\Parameter(
     *         name="emailUser",
     *         in="query",
     *         description="Email of the user",
     *         required=true,
     *         @OA\Schema(type="string", default="test@test.fr")
     *     ),
     *     @OA\Parameter(
     *         name="passwordUser",
     *         in="query",
     *         description="Password of the user",
     *         required=true,
     *         @OA\Schema(type="string", default="test")
     *     ),
     *    @OA\Response(
     *        response=401,
     *        description="Credentials not valid",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Resource Not Found"
     *    ),
     *    @OA\Response(
     *      response=200,
     *      description="Login",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="token",
     *          default="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwXC9hcGlcL3YxXC9sb2dpbiIsImlhdCI6MTYxMjUxNzQ2MSwiZXhwIjoxNjEyNTIxMDYxLCJuYmYiOjE2MTI1MTc0NjEsImp0aSI6IlBFVnhXME9ma0FUZnJLZjMiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjciLCJpZFVzZXIiOjEsImlkUm9sZVVzZXIiOjF9.Hw68eA7W5SXSqc7I1dyRYPkcABk11vfdFeVgfFPLuYI",
     *          description="Token",
     *        ),
     *        @OA\Property(
     *          property="token_type",
     *          default="bearer",
     *          description="Type of token",
     *        ),
     *        @OA\Property(
     *          property="expires_in",
     *          default="3600",
     *          description="Expiration",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="success",
     *          description="Success of fail",
     *        ),
     *      )
     *    )
     * )
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

    /**
     * @OA\Get(
     *   path="/api/v1/users",
     *   summary="Return all users",
     *   tags={"User Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(ref="#/components/parameters/get_request_parameter_limit"),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of users",
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
     *     )
     *   )
     * )
     */
    public function getUsers(Request $request)
    {
        $users = User::all();

        return response()->json($users, 200);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/users/{id}",
     *   summary="Return a user",
     *   tags={"User Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the user to get",
     *     @OA\Schema(
     *       type="number", default=1
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
     *       description="User recovery failed!"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="One user",
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
     *     )
     *   ),
     * )
     */

    public function getUser($id)
    {
        try {
            $user = User::all()->where('idUser', $id)->first();

            if (empty($user))
                return response()->json(['message' => "The user $id doesn't exist", 'status' => 'fail'], 404);

            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found!' . $e->getMessage()], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/users",
     *   summary="Add a user",
     *   tags={"User Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="firstnameUser",
     *     in="query",
     *     required=true,
     *     description="First name of the user to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="lastnameUser",
     *     in="query",
     *     required=true,
     *     description="Last name of the user to add",
     *     @OA\Schema(
     *       type="string", default="last"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="emailUser",
     *     in="query",
     *     required=true,
     *     description="Email of the user to add",
     *     @OA\Schema(
     *       type="string", default="test@test.fr"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="passwordUser",
     *     in="query",
     *     required=true,
     *     description="password of the user to add",
     *     @OA\Schema(
     *       type="string", default="test"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="passwordUser_confirmation",
     *     in="query",
     *     required=true,
     *     description="Confirmation password of the user to add",
     *     @OA\Schema(
     *       type="string", default="test"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="idRoleUser",
     *     in="query",
     *     required=true,
     *     description="Role id of the user to add",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Not created",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="User created",
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
     *     )
     *   ),
     * )
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
            'created_by' => 'integer',
            'updated_by' => 'integer',
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

            // Return successful response
            return response()->json(['user' => $user, 'message' => 'User successfully created!', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'User Registration Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/users/{id}",
     *   summary="Update a user",
     *   tags={"User Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the user to update",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Property(
     *     property="idUser",
     *     default="1",
     *     description="id of the user",
     *   ),
     *   @OA\Parameter(
     *     name="firstnameUser",
     *     in="query",
     *     description="First name of the user to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="lastnameUser",
     *     in="query",
     *     description="Last name of the user to add",
     *     @OA\Schema(
     *       type="string", default="last"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="emailUser",
     *     in="query",
     *     description="Email of the user to add",
     *     @OA\Schema(
     *       type="string", default="test@test.fr"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="passwordUser",
     *     in="query",
     *     description="password of the user to add",
     *     @OA\Schema(
     *       type="string", default="test"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="passwordUser_confirmation",
     *     in="query",
     *     description="Confirmation password of the user to add",
     *     @OA\Schema(
     *       type="string", default="test"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="idRoleUser",
     *     in="query",
     *     description="Role id of the user to add",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",  
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Not updated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User updated",
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
     *     )
     *   ),
     * )
     */
    public function updateUser($id, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'lastnameUser' => 'string',
            'firstnameUser' => 'string',
            'emailUser' => 'email|unique:users,emailUser,' . $request->id . ',idUser',
            'passwordUser' => 'confirmed',
            'idRoleUser' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ]);

        try {
            // Update
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

            //return successful response
            return response()->json(['user' => $user, 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'User Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/users/{id}",
     *   summary="Delete a user",
     *   tags={"User Controller"},
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
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Deletion failed!",
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
     *     )
     *   ),
     * )
     */
    public function deleteUser($id)
    {
        try {
            $user = User::find($id);

            $user->delete();

            return response()->json(['user' => $user, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'User deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/logout",
     *   summary="Log out",
     *   tags={"User Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Response(
     *       response=401,
     *       description="Not authorized",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Logged out",
     *   ),
     * )
     */
    public function logout()
    {
        $this->guard()->logout();
    }

    public function guard()
    {
        return Auth::guard();
    }
}
