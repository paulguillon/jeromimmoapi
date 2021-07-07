<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\VisitData;

class VisitController extends Controller
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
     *   path="/api/v1/visits/{id}/data",
     *   summary="Return all data of specific visit",
     *   tags={"VisitData Controller"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the visit",
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
     *         property="idVisitData",
     *         default=1,
     *         description="Id of the visit data",
     *       ),
     *       @OA\Property(
     *         property="keyVisitData",
     *         default="Any key",
     *         description="Key of the visit data",
     *       ),
     *       @OA\Property(
     *         property="valueVisitData",
     *         default="Any value",
     *         description="Value of the visit data",
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
     *         property="idVisit",
     *         default=1,
     *         description="ID of the visit that this data is related to",
     *       ),
     *     )
     *   )
     * )
     */
    public function getAllData($id)
    {
        try {
            //if visit doesn't exists
            if (!$this->existVisit($id))
                return response()->json(['data' => null, 'message' => "Visit doesn't exists", 'status' => 'fail'], 404);

            $data = array_values(VisitData::all()->where('idVisit', $id)->toArray());

            return response()->json(['total' => count($data), 'data' => $data, 'message' => 'Property data successfully retrieved', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/v1/visits/{id}/data/{key}",
     *   summary="Return specific data of the specified visit",
     *   tags={"VisitData Controller"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the concerned visit",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="key of the visit to get",
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
     *         property="idVisitData",
     *         default="1",
     *         description="key of the visit",
     *       ),
     *       @OA\Property(
     *         property="keyVisitData",
     *         default="key",
     *         description="key of the visit",
     *       ),
     *       @OA\Property(
     *         property="valueVisitData",
     *         default="Any value",
     *         description="Value of the visit",
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
     *         property="idVisit",
     *         default="1",
     *         description="Property associated with the data",
     *       ),
     *     )
     *   ),
     * )
     */
    public function getVisitData($id, $key)
    {
        try {
            //if visit doesn't exists
            if (!$this->existVisit($id))
                return response()->json(['data' => null, 'message' => "Visit doesn't exists", 'status' => 'fail'], 404);

            $visitData = VisitData::all()
                ->where('idVisit', $id)
                ->where('keyVisitData', $key)
                ->first();

            //key doesn't exists
            if (!$visitData)
                return response()->json(['data' => null, 'message' => "No data for this key", 'status' => 'fail'], 404);

            return response()->json(['data' => $visitData, 'message' => 'Data successfully retrieved!', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/visits/{id}/data",
     *   summary="Add a data to a specific visit",
     *   tags={"VisitData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the visit",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyVisitData",
     *     in="query",
     *     required=true,
     *     description="Key of the visit data",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueVisitData",
     *     in="query",
     *     required=true,
     *     description="Value of the visit data",
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
     *         property="idVisitData",
     *         default=1,
     *         description="Id of the visit",
     *       ),
     *       @OA\Property(
     *         property="keyVisitData",
     *         default="Some key",
     *         description="Key to add",
     *       ),
     *       @OA\Property(
     *         property="valueVisitData",
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
    public function addVisitData($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyVisitData' => 'required|string',
            'valueVisitData' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            //if visit or users doesn't exist
            $created_by = User::all()->where('idUser', $request->input('created_by'))->first();
            $updated_by = User::all()->where('idUser', $request->input('updated_by'))->first();
            if (!$this->existVisit($id))
                return response()->json(['data' => null, 'message' => "Unknown Property", 'status' => 'fail'], 404);
            if (!$created_by)
                return response()->json(['data' => null, 'message' => "Creator unknown", 'status' => 'fail'], 404);
            if (!$updated_by)
                return response()->json(['data' => null, 'message' => "User unknown", 'status' => 'fail'], 404);

            //if visit data already exists
            $exist = VisitData::all()
                ->where('keyVisitData', $request->input('keyVisitData'))
                ->where('idVisit', $id)
                ->first();
            if ($exist)
                return response()->json(['data' => null, 'message' => "Data already exists", 'status' => 'fail'], 404);

            //creation of the new data
            $visitData = new VisitData;
            $visitData->keyVisitData = $request->input('keyVisitData');
            $visitData->valueVisitData = $request->input('valueVisitData');
            $visitData->created_by = (int)$request->input('created_by');
            $visitData->updated_by = (int)$request->input('updated_by');
            $visitData->idVisit = (int)$id;
            $visitData->save();

            // Return successful response
            return response()->json(['visitData' => $visitData, 'message' => 'Property data successfully created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property Data addition failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/visits/{id}/data/{key}",
     *   summary="Update a visit data",
     *   tags={"VisitData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Key of the visit related to the data to update",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the visit data to update",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyVisitData",
     *     in="query",
     *     required=false,
     *     description="New keyVisitData",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueVisitData",
     *     in="query",
     *     required=false,
     *     description="New valueVisitData",
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
     *     name="idVisit",
     *     in="query",
     *     required=false,
     *     description="New idVisit",
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
     *     description="Property data updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisitData",
     *         default=1,
     *         description="Id of the visit data",
     *       ),
     *       @OA\Property(
     *         property="keyVisitData",
     *         default="thumbnail",
     *         description="Key of the visit data",
     *       ),
     *       @OA\Property(
     *         property="valueVisitData",
     *         default="any value",
     *         description="Value of the visit data",
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
     *         property="idVisit",
     *         default=1,
     *         description="ID of visit this data is related to",
     *       ),
     *     )
     *   ),
     * )
     */
    public function updateVisitData($id, $key, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'keyVisitData' => 'string',
            'valueVisitData' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'idVisit' => 'integer',
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

            //if visit doesn't exists
            if (!$this->existVisit($id))
                return response()->json(['data' => null, 'message' => "Unknown Property", 'status' => 'fail'], 404);

            //test if the new key already exists
            $newKeyExist = VisitData::all()
                ->where('idVisit', $id)
                ->where('keyVisitData', $request->input('keyVisitData'))
                ->first();
            if ($newKeyExist)
                return response()->json(['message' => 'Data with this key already exists', 'status' => 'fail'], 404);

            // update
            $visitData = VisitData::all()
                ->where('idVisit', $id)
                ->where('keyVisitData', $key)
                ->first();
            if (!$visitData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            if ($request->input('keyVisitData') !== null)
                $visitData->keyVisitData = $request->input('keyVisitData');
            if ($request->input('valueVisitData') !== null)
                $visitData->valueVisitData = $request->input('valueVisitData');
            if ($request->input('created_by') !== null)
                $visitData->created_by = (int)$request->input('created_by');
            if ($request->input('updated_by') !== null)
                $visitData->updated_by = (int)$request->input('updated_by');
            if ($request->input('idVisit') !== null)
                $visitData->idVisit = (int)$request->input('idVisit');

            $visitData->update();

            // Return successful response
            return response()->json(['data' => $visitData, 'message' => 'Data successfully updated', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Property data Update Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/visits/{id}/data/{key}",
     *   summary="Delete a visit data",
     *   tags={"VisitData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the visit data to delete",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the visit data to delete",
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
     *         property="keyVisitData",
     *         default="Any key",
     *         description="Key of the visit data",
     *       ),
     *       @OA\Property(
     *         property="valueVisitData",
     *         default="any value",
     *         description="Value of the visit data",
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
     *         property="idVisit",
     *         default=1,
     *         description="ID of visit this data was related to",
     *       )
     *      )
     *   ),
     * )
     */
    public function deleteVisitData($id, $key)
    {
        try {
            $visitData = VisitData::all()
                ->where('idVisit', $id)
                ->where('keyVisitData', $key)
                ->first();

            if (!$visitData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            $visitData->delete();

            return response()->json(['data' => $visitData, 'message' => 'Data successfully deleted', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Data deletion failed!', 'status' => 'fail'], 409);
        }
    }

    private function existVisit($id)
    {
        $visit = Visit::all()
            ->where('idVisit', $id)
            ->first();
        return (bool) $visit;
    }
}
