<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Role;


class RoleController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        $this->middleware('auth:api');
    }



    /**
     * @OA\Get(
     *   path="/api/v1/roles",
     *   summary="Return all roles",
     *   tags={"Roles Controller"},
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
     *     description="List of roles",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idRole",
     *         default="1",
     *         description="id of the role",
     *       ),
     *   @OA\Property(
     *         property="roleName",
     *         default="Admin",
     *         description="Name of the role",
     *       ),
     * @OA\Property(
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
    public function  getRoles(Request $request)
    {
        return response()->json(['roles' =>  Role::all()], 200);
    }



    /**
     * @OA\Get(
     *   path="/api/v1/roles/{id}",
     *   summary="Return a role",
     *   tags={"Roles Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the role to get",
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
     * @OA\Response(
     *       response=500,
     *       description="role not found",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="The role ? doesn't exist",
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
     *     description="One roles",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idRole",
     *         default="1",
     *         description="id of the role",
     *       ),
     *   @OA\Property(
     *         property="roleName",
     *         default="Admin",
     *         description="Name of the role",
     *       ),
     *  @OA\Property(
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

    public function getRole($id)
    {
        try {

            $role = Role::all()->where('idRole', $id)->first();

            return response()->json(['role' => $role], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'role user not found ' . $e->getMessage()], 404);
        }
    }
    /**
     * @OA\Post(
     *   path="/api/v1/roles",
     *   summary="Add a Role",
     *   tags={"Roles Controller"},
     * security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="roleName",
     *     in="query",
     *     required=true,
     *     description="Name of new Role",
     *     @OA\Schema(
     *       type="string", default="Admin"
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
     *       description="Role Not created",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *    
     *   @OA\Response(
     *     response=201,
     *     description="Role Created",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idRole",
     *         default="1",
     *         description="id of the Role",
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

    public function addRole(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'roleName' => 'required|string',
            'created_by' => 'required|string',
            'updated_by' => 'required|string'
        ]);

        try {

            $role = new Role;
            $role->roleName = $request->input('roleName');
            $role->created_by = $request->input('created_by');
            $role->updated_by = $request->input('updated_by');
            $role->save();

            //return successful response
            return response()->json(['role' => $role, 'message' => 'CREATED ROLE'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Role Creation failed' . $e->getMessage()], 409);
        }
    }


    /**
     * @OA\Patch(
     *   path="/api/v1/roles/{id}",
     *   summary="Update a roles",
     *   tags={"Roles Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the role to update",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Property(
     *     property="idRole",
     *     default="1",
     *     description="id of the role",
     *   ),
     *  
     *   @OA\Parameter(
     *     name="roleName",
     *     required=true,
     *     in="query",
     *     description="name of role to update",
     *     @OA\Schema(
     *       type="string", default="roleName"
     *     )
     *   ),
     *  @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     description="ID of the logged user",
     *     required=true,
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     description="ID of the logged user",
     *     required=true,
     *     @OA\Schema(
     *       type="number", default="1"
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
     *     response=200,
     *     description="role updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idRoles",
     *         default="1",
     *         description="id of the role",
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
    public function updateRole($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'roleName' => 'required|string',
            'created_by' => 'integer',
            'updated_by' => 'required|string'
        ]);

        try {
            $role = Role::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);

            if ($request->input('roleName') !== null)
                $role->roleName = $request->input('roleName');
            if ($request->input('created_by') !== null)
                $role->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $role->updated_by = $request->input('updated_by');

            $role->update();

            //return successful response
            return response()->json(['role' => $role, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Role Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }


/**
     * @OA\Delete(
     *   path="/api/v1/roles/{id}",
     *   summary="Delete a role",
     *   tags={"Roles Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the role to delete",
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
     * 
     *   @OA\Response(
     *     response=200,
     *     description="role deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idRole",
     *         default="1",
     *         description="id of the role",
     *       ),
     *    @OA\Property(
     *         property="roleName",
     *         default="test",
     *         description="name of the role",
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


    public function deleteRole($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return response()->json(['role' => $role, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Role deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
