<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agency;
use App\Models\AgencyData;

class AgencyDataController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        $this->middleware('auth:api', ['accept' => []]);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/agency/{id}/data",
     *   summary="Return all data of specific agency",
     *   tags={"AgencyData Controller"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the agency",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
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
     *     response=200,
     *     description="List of data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idAgencyData",
     *         default=1,
     *         description="Id of the agency data",
     *       ),
     *       @OA\Property(
     *         property="keyAgencyData",
     *         default="Any key",
     *         description="Key of the agency data",
     *       ),
     *       @OA\Property(
     *         property="valueAgencyData",
     *         default="Any value",
     *         description="Value of the agency data",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="ID of user who did the last update",
     *       ),
     *       @OA\Property(
     *         property="idAgency",
     *         default=1,
     *         description="ID of the agency that this data is related to",
     *       ),
     *     )
     *   )
     * )
     */
    public function getAllData($id)
    {
        try {
            //if agency doesn't exists
            if (!$this->existAgency($id))
                return response()->json(['data' => null, 'message' => "Agency doesn't exists", 'status' => 'fail'], 404);

            $data = array_values(AgencyData::all()->where('idAgency', $id)->toArray());

            return response()->json(['total' => count($data), 'data' => $data, 'message' => 'Agency data successfully retrieved', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/v1/agency/{id}/data/{key}",
     *   summary="Return specific data of the specified agency",
     *   tags={"AgencyData Controller"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the concerned agency",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="key of the agency to get",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
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
     *   @OA\Response(
     *     response=200,
     *     description="Requested data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idAgencyData",
     *         default="1",
     *         description="key of the agency",
     *       ),
     *       @OA\Property(
     *         property="keyAgencyData",
     *         default="key",
     *         description="key of the agency",
     *       ),
     *       @OA\Property(
     *         property="valueAgencyData",
     *         default="Any value",
     *         description="Value of the agency",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Who did the last update",
     *       ),
     *       @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Agency associated with the data",
     *       ),
     *     )
     *   ),
     * )
     */
    public function getAgencyData($id, $key)
    {
        try {
            //if agency doesn't exists
            if (!$this->existAgency($id))
                return response()->json(['data' => null, 'message' => "Agency doesn't exists", 'status' => 'fail'], 404);

            $agencyData = AgencyData::all()
                ->where('idAgency', $id)
                ->where('keyAgencyData', $key)
                ->first();

            //key doesn't exists
            if (!$agencyData)
                return response()->json(['data' => null, 'message' => "No data for this key", 'status' => 'fail'], 404);

            return response()->json(['data' => $agencyData, 'message' => 'Data successfully retrieved!', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/agency/{id}/data",
     *   summary="Add a data to a specific agency",
     *   tags={"AgencyData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the agency",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyAgencyData",
     *     in="query",
     *     required=true,
     *     description="Key of the agency data",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueAgencyData",
     *     in="query",
     *     required=true,
     *     description="Value of the agency data",
     *     @OA\Schema(
     *       type="string", default="Any value"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the creator",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the last user who changed this line",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Unknown Property"
     *   ),
     *   @OA\Response(
     *     response=409,
     *     description="Data addition failed!",
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Data added",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idAgencyData",
     *         default=1,
     *         description="Id of the agency",
     *       ),
     *       @OA\Property(
     *         property="keyAgencyData",
     *         default="Some key",
     *         description="Key to add",
     *       ),
     *       @OA\Property(
     *         property="valueAgencyData",
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
     *         description="User's id who this new data is related to",
     *       ),
     *     )
     *   ),
     * )
     */
    public function addAgencyData($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyAgencyData' => 'required|string',
            'valueAgencyData' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            //if agency or users doesn't exist
            $created_by = User::all()->where('idUser', $request->input('created_by'))->first();
            $updated_by = User::all()->where('idUser', $request->input('updated_by'))->first();
            if (!$this->existAgency($id))
                return response()->json(['data' => null, 'message' => "Unknown Property", 'status' => 'fail'], 404);
            if (!$created_by)
                return response()->json(['data' => null, 'message' => "Creator unknown", 'status' => 'fail'], 404);
            if (!$updated_by)
                return response()->json(['data' => null, 'message' => "User unknown", 'status' => 'fail'], 404);

            //if agency data already exists
            $exist = AgencyData::all()
                ->where('keyAgencyData', $request->input('keyAgencyData'))
                ->where('idAgency', $id)
                ->first();
            if ($exist)
                return response()->json(['data' => null, 'message' => "Data already exists", 'status' => 'fail'], 404);

            //creation of the new data
            $agencyData = new AgencyData;
            $agencyData->keyAgencyData = $request->input('keyAgencyData');
            $agencyData->valueAgencyData = $request->input('valueAgencyData');
            $agencyData->created_by = (int)$request->input('created_by');
            $agencyData->updated_by = (int)$request->input('updated_by');
            $agencyData->idVisit = (int)$id;
            $agencyData->save();

            // Return successful response
            return response()->json(['agencyData' => $agencyData, 'message' => 'Agency data successfully created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency Data addition failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/agency/{id}/data/{key}",
     *   summary="Update an agency data",
     *   tags={"AgencyData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Key of the agency related to the data to update",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the agency data to update",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyAgencyData",
     *     in="query",
     *     required=false,
     *     description="New keyAgencyData",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueAgencyData",
     *     in="query",
     *     required=false,
     *     description="New valueAgencyData",
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
     *     description="New modifier",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="idAgency",
     *     in="query",
     *     required=false,
     *     description="New idAgency",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
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
     *   @OA\Response(
     *     response=200,
     *     description="Agency data updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idAgencyData",
     *         default=1,
     *         description="Id of the agency data",
     *       ),
     *       @OA\Property(
     *         property="keyAgencyData",
     *         default="thumbnail",
     *         description="Key of the agency data",
     *       ),
     *       @OA\Property(
     *         property="valueAgencyData",
     *         default="any value",
     *         description="Value of the agency data",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="ID of user that modifier this data",
     *       ),
     *       @OA\Property(
     *         property="idAgency",
     *         default=1,
     *         description="ID of agency this data is related to",
     *       ),
     *     )
     *   ),
     * )
     */
    public function updateAgencyData($id, $key, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'keyAgencyData' => 'string',
            'valueAgencyData' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'idAgency' => 'integer',
        ]);

        try {
            //if users doesn't exist
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

            //if agency doesn't exists
            if (!$this->existAgency($id))
                return response()->json(['data' => null, 'message' => "Unknown Property", 'status' => 'fail'], 404);

            //test if the new key already exists
            $newKeyExist = AgencyData::all()
                ->where('idAgency', $id)
                ->where('keyAgencyData', $request->input('keyAgencyData'))
                ->first();
            if ($newKeyExist)
                return response()->json(['message' => 'Data with this key already exists', 'status' => 'fail'], 404);

            // update
            $agencyData = AgencyData::all()
                ->where('idAgency', $id)
                ->where('keyAgencyData', $key)
                ->first();
            if (!$agencyData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            if ($request->input('keyAgencyData') !== null)
                $agencyData->keyAgencyData = $request->input('keyAgencyData');
            if ($request->input('valueAgencyData') !== null)
                $agencyData->valueAgencyData = $request->input('valueAgencyData');
            if ($request->input('created_by') !== null)
                $agencyData->created_by = (int)$request->input('created_by');
            if ($request->input('updated_by') !== null)
                $agencyData->updated_by = (int)$request->input('updated_by');
            if ($request->input('idAgency') !== null)
                $agencyData->idAgency = (int)$request->input('idAgency');

            $agencyData->update();

            // Return successful response
            return response()->json(['data' => $agencyData, 'message' => 'Data successfully updated', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Agency data Update Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/agency/{id}/data/{key}",
     *   summary="Delete an agency data",
     *   tags={"AgencyData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the agency data to delete",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the agency data to delete",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
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
     *   @OA\Response(
     *     response=200,
     *     description="Property data deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="keyAgencyData",
     *         default="Any key",
     *         description="Key of the agency data",
     *       ),
     *       @OA\Property(
     *         property="valueAgencyData",
     *         default="any value",
     *         description="Value of the agency data",
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
     *         property="idAgency",
     *         default=1,
     *         description="ID of agency this data was related to",
     *       )
     *      )
     *   ),
     * )
     */
    public function deleteAgencyData($id, $key)
    {
        try {
            $agencyData = AgencyData::all()
                ->where('idAgency', $id)
                ->where('keyAgencyData', $key)
                ->first();

            if (!$agencyData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            $agencyData->delete();

            return response()->json(['data' => $agencyData, 'message' => 'Data successfully deleted', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Data deletion failed!', 'status' => 'fail'], 409);
        }
    }

    private function existAgency($id)
    {
        $agency = Agency::all()
            ->where('idAgency', $id)
            ->first();
        return (bool) $agency;
    }
}