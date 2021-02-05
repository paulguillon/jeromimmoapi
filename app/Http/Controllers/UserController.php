<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Parameter(
 *   parameter="get_users_request_parameter_limit",
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
class UserController extends Controller
{

    /**
  * @OA\Get(
  *   path="/users",
  *   summary="Return the list of users",
  *   tags={"Hello"},
  *   @OA\Parameter(ref="#/components/parameters/get_users_request_parameter_limit"),
   *    @OA\Response(
  *      response=200,
  *      description="List of users",
  *      @OA\JsonContent(
  *        @OA\Property(
  *          property="data",
  *          description="List of users",
  *          @OA\Schema(
  *            type="array",
  *            @OA\Items(ref="#/components/schemas/UserSchema")
  *          )
  *        )
  *      )
  *    )
  * )
  */
    public function index (Request $request)
    {
    $users = User::paginate($request->get("limit", 10));
    return ["data" => $users];
    }

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
     *   @OA\Parameter(ref="#/components/parameters/get_users_request_parameter_limit"),
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
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Id of user who modified this one",
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
     *   )
     * )
     */
    public function getUsers(Request $request)
    {
        $users = User::all();

        for ($i = 0; $i < count($users); $i++) {
            $user = $users[$i];

            $user['data'] = $this->getAllData($user->idUser);
        }

        return response()->json(['users' => $users], 200);
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
     *       response=500,
     *       description="User not found",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="The user ? doesn't exist",
     *          description="Message",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="fail",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="One user",
     *     @OA\JsonContent(
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
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Id of user who modified this one",
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

    public function getUser($id)
    {
        try {
            $user = User::all()->where('idUser', $id)->first();

            if (empty($user))
                return response()->json(['message' => "The user $id doesn't exist", 'status' => 'fail'], 500);

            $user['data'] = $this->getAllData($id);

            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'User not found!' . $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/users",
     *   summary="Add a user",
     *   tags={"User Controller"},
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
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     required=true,
     *     description="First name of the user to add",
     *     @OA\Schema(
     *       type="string", default="{'cle':'valeur','deuxiemecle':'deuxiemevaleur'}"
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
     *   @OA\Response(
     *     response=201,
     *     description="User created",
     *     @OA\JsonContent(
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
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Id of user who modified this one",
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
                if (!$this->_addData($user->idUser, $request))
                    return response()->json(['message' => 'User data not added!', 'status' => 'fail'], 500);
            }

            // Return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED', 'status' => 'success'], 201);
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
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     description="First name of the user to add",
     *     @OA\Schema(
     *       type="string", default="{'cle':'valeur','deuxiemecle':'deuxiemevaleur'}"
     *     )
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
     *   @OA\Response(
     *     response=200,
     *     description="User updated",
     *     @OA\JsonContent(
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
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Id of user who modified this one",
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

            'data' => 'string',
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

            // Update data
            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->updateData($user->idUser, $key, $value))
                        return response()->json(['message' => 'User data Update Failed!', 'status' => 'fail'], 500);
                }
            }

            //return successful response
            return response()->json(['user' => $user, 'data' => $this->getAllData($user->idUser), 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
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
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Id of user who modified this one",
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
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $userData = $this->getAllData($id);

            //delete les data
            if ($userData !== null) {
                if (!$this->deleteData($id))
                    return response()->json(['message' => 'User Deletion Failed!', 'status' => 'fail'], 500);
            }

            $user->delete();

            return response()->json(['user' => $user, 'data' => $userData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'User deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/users/data/{id}",
     *   summary="Add user data",
     *   tags={"User Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     required=true,
     *     description="data to add",
     *     @OA\Schema(
     *       type="string", default="{}"
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data not created",
     *   ),
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
     *   @OA\Response(
     *     response=201,
     *     description="User data created",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="data",
     *          default="[]",
     *          description="data",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="success",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     * )
     */
    public function addData($id, Request $request)
    {
        try {
            if (!$this->_addData($id, $request))
                return response()->json(['message' => 'Not all data has been added', 'status' => 'fail'], 409);

            //return successful response
            return response()->json(['data' => $this->getAllData($id), 'message' => 'Data created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'User data not added!', 'status' => 'fail'], 409);
        }
    }

    //fonction utilisée par la route et lors de la creation de user pour ajouter toutes les data
    public function _addData($idUser, $request)
    {
        $data = (array)json_decode($request->input('data'), true);

        try {
            foreach ($data as $key => $value) {

                $userData = new UserData;
                $userData->keyUserData = $key;
                $userData->valueUserData = $value;
                $userData->created_by = $request->input('created_by');
                $userData->updated_by = $request->input('updated_by');
                $userData->idUser = $idUser;

                $userData->save();
            }

            // Return successful response
            return true;
        } catch (\Exception $e) {
            // Return error message
            return false;
        }
    }

    public function getAllData($idUser)
    {
        $data = array();
        foreach (UserData::all()->where('idUser', $idUser) as $value) {
            array_push($data, $value);
        }
        return response()->json($data, 200)->original;
    }

    public function getData($idUser, $key)
    {
        return response()->json(
            UserData::all()
                ->where('idUser', $idUser)
                ->where('keyUserData', $key),
            200
        );
    }

    public function updateData($idUser, $key, $value)
    {
        try {
            $userData = UserData::all()
                ->where('idUser', $idUser)
                ->where('keyUserData', $key)
                ->first();

            if ($userData == null)
                return false;

            $userData->valueUserData = $value;
            $userData->update();

            return true;
        } catch (\Exception $e) {
            return false;
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
