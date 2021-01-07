<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Role;


class RoleController extends Controller
{


    /**
     * Create Role
     *
     * @param  Request  $request
     * @return Response
     */

    public function createRole(Request $request)
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

    public function oneUserRole($id)
    {
        try {

            $role = Role::all()->where('idRole', $id)->first();

            return response()->json(['role' => $role], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'role user not found ' . $e->getMessage()], 404);
        }
    }
}
