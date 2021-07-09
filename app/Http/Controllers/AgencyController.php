<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agency;
use App\Models\User;

class AgencyController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        // EXCEPTIONS mises en place pour créations de controllers en attendant de remettre en place les droits d'accès
        $this->middleware('auth:api', ['except' => ['getAgencies', 'getAgency']]);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/agency",
     *   summary="Return all agencies",
     *   tags={"Agency Controller"},
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
     *     description="List of agencies",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Id of the agency",
     *       ),
     *       @OA\Property(
     *         property="nameAgency",
     *         default="Agency name",
     *         description="Name of the agency",
     *       ),
     *       @OA\Property(
     *         property="zipCodeAgency",
     *         default="zip code",
     *         description="Zip code of the agency",
     *       ),
     *       @OA\Property(
     *         property="cityAgency",
     *         default="city",
     *         description="City of the agency",
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

    public function getAgencies(Request $request)
    {
        $agencies = Agency::all();
        return response()->json($agencies, 200);
    }
    /**
     * @OA\Get(
     *   path="/api/v1/agency/{id}",
     *   summary="Return a agency",
     *   tags={"Agency Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the agency to get",
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
     *     response=200,
     *     description="One agency",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Id of the agency",
     *       ),
     *       @OA\Property(
     *         property="nameAgency",
     *         default="name",
     *         description="Name of the agency",
     *       ),
     *       @OA\Property(
     *         property="zipCodeAgency",
     *         default="zip code",
     *         description="Zip code of the agency",
     *       ),
     *       @OA\Property(
     *         property="cityAgency",
     *         default="city",
     *         description="City of the agency",
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
     *         description="Agency data",
     *       ),
     *     )
     *   ),
     * )
     */
    public function getAgency($id)
    {
        try {
            $agency = Agency::all()
                ->where('idAgency', $id)
                ->first();

            return response()->json($agency, 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Agency not found!' . $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/agency",
     *   summary="Add a agency",
     *   tags={"Agency Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="nameAgency",
     *     in="query",
     *     required=true,
     *     description="Name of the agency to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="zipCodeAgency",
     *     in="query",
     *     required=true,
     *     description="Zip code of the agency to add",
     *     @OA\Schema(
     *       type="string", default="Zip code"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="cityAgency",
     *     in="query",
     *     required=true,
     *     description="City of the agency to add",
     *     @OA\Schema(
     *       type="string", default="City"
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
     *     description="Agency created",
     *     @OA\JsonContent(
     *     @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Id of the agency",
     *       ),
     *       @OA\Property(
     *         property="nameAgency",
     *         default="name",
     *         description="Name of the agency",
     *       ),
     *       @OA\Property(
     *         property="zipCodeAgency",
     *         default="zipCode",
     *         description="Zip Code of the Agency",
     *       ),
     *       @OA\Property(
     *         property="cityAgency",
     *         default="city Agency",
     *         description="City of the agency",
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

    public function addAgency(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameAgency' => 'required|string',
            'zipCodeAgency' => 'required|integer',
            'cityAgency' => 'required|string',
            'keyAgencyData' => 'string',
            'valueAgencyData' => 'string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $agency = new Agency;
            $agency->nameAgency = $request->input('nameAgency');
            $agency->zipCodeAgency = $request->input('zipCodeAgency');
            $agency->cityAgency = $request->input('cityAgency');
            $agency->created_by = $request->input('created_by');
            $agency->updated_by = $request->input('updated_by');

            //test if the creator exists
            $exist = User::find($request->input('created_by'));
            if (!$exist)
                return response()->json(['document' => null, 'message' => 'Unknown creator', 'status' => 'fail'], 404);
            $agency->created_by = $request->input('created_by');

            //test if the user exists
            $exist = User::find($request->input('updated_by'));
            if (!$exist)
                return response()->json(['document' => null, 'message' => 'Unknown user', 'status' => 'fail'], 404);
            $agency->updated_by = $request->input('updated_by');

            $agency->save();

            // Return successful response
            return response()->json(['agency' => $agency, 'message' => 'Agency successfully created!', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // return error message
            return response()->json(['message' => 'Agency Registration Failed!', $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/agency/{id}",
     *   summary="Update an agency",
     *   tags={"Agency Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the agency to update",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="nameAgency",
     *     in="query",
     *     description="Name of the agency to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="zipCodeAgency",
     *     in="query",
     *     description="Zip Code of the agency to add",
     *     @OA\Schema(
     *       type="string", default="Zip Code"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="cityAgency",
     *     in="query",
     *     description="City of the agency to add",
     *     @OA\Schema(
     *       type="string", default="City"
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
     *       response=409,
     *       description="Not updated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Agency data not updated",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Agency data not updated",
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
     *     description="Agency updated",
     *     @OA\JsonContent(
     *     @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Id of the agency",
     *       ),
     *       @OA\Property(
     *         property="nameagency",
     *         default="Name",
     *         description="Name of the agency",
     *       ),
     *       @OA\Property(
     *         property="zipCodeAgency",
     *         default="ZipCode",
     *         description="sip code of the agency",
     *       ),
     *       @OA\Property(
     *         property="city",
     *         default="CityLe Havre",
     *         description="City of the agency",
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
    public function updateAgency($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameAgency' => 'string',
            'zipCodeAgency' => 'string|min:5|max:5',
            'cityAgency' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer'
        ]);

        try {
            // Update
            $agency = Agency::findOrFail($id);
            if ($request->input('nameAgency') !== null)
                $agency->nameAgency = $request->input('nameAgency');
            if ($request->input('zipCodeAgency') !== null)
                $agency->zipCodeAgency = $request->input('zipCodeAgency');
            if ($request->input('cityAgency') !== null)
                $agency->cityAgency = $request->input('cityAgency');
            if ($request->input('created_by') !== null){
            //test if the creator exists
                $exist = User::find($request->input('created_by'));
                if (!$exist)
                    return response()->json(['document' => null, 'message' => 'Unknown creator', 'status' => 'fail'], 404);
                    //Update if ok
                $agency->created_by = $request->input('created_by');
            }
                if ($request->input('updated_by') !== null) {
                //test if the creator exists
                $exist = User::find($request->input('updated_by'));
                if (!$exist)
                    return response()->json(['document' => null, 'message' => 'Unknown user', 'status' => 'fail'], 404);
                //update if ok
                $agency->updated_by = $request->input('updated_by');
            }

            $agency->update();

            //return successful response
            return response()->json(['agency' => $agency, 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/agency/{id}",
     *   summary="Delete an agency",
     *   tags={"Agency Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the agency to delete",
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
     *     response=200,
     *     description="Agency deleted",
     *     @OA\JsonContent(
     *     @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Id of the agency",
     *   ),
     *       @OA\Property(
     *         property="nameAgency",
     *         default="Name",
     *         description="Name of the agency",
     *       ),
     *       @OA\Property(
     *         property="zipCodeAgency",
     *         default="ZipCode",
     *         description="zipCode of the agency",
     *       ),
     *       @OA\Property(
     *         property="cityAgency",
     *         default="City",
     *         description="City of the agency",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of agency who created this one",
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
    public function deleteAgency($id)
    {
        try {
            $agency = Agency::findOrFail($id);

            $agency->delete();

            return response()->json(['agency' => $agency, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
