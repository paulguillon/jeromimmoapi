<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyData;
use App\Models\User;

class PropertyDataController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        $this->middleware('auth:api', ['except' => ['getAllData', 'getPropertyData']]);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/properties/{id}/data",
     *   summary="Return all data of specific property",
     *   tags={"PropertyData Controller"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the property",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idPropertyData",
     *         default=1,
     *         description="Id of the property data",
     *       ),
     *       @OA\Property(
     *         property="keyPropertyData",
     *         default="Any key",
     *         description="Key of the property data",
     *       ),
     *       @OA\Property(
     *         property="valuePropertyData",
     *         default="Any value",
     *         description="Value of the property data",
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
     *         property="idProperty",
     *         default=1,
     *         description="ID of the property that this data is related to",
     *       ),
     *     )
     *),
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
            //if property doesn't exists
            if (!$this->existProperty($id))
                return response()->json(['data' => null, 'message' => "Property doesn't exists", 'status' => 'fail'], 404);

            $data = array_values(PropertyData::all()->where('idProperty', $id)->toArray());

            return response()->json(['total' => count($data), 'data' => $data, 'message' => 'Property data successfully retrieved', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/v1/properties/{id}/data/{key}",
     *   summary="Return specific data of the specified property",
     *   tags={"PropertyData Controller"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the concerned property",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="key of the property to get",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Requested data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idPropertyData",
     *         default="1",
     *         description="ID of the property",
     *       ),
     *       @OA\Property(
     *         property="keyPropertyData",
     *         default="key",
     *         description="Key of the property",
     *       ),
     *       @OA\Property(
     *         property="valuePropertyData",
     *         default="Any value",
     *         description="Value of the property",
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
     *         property="idProperty",
     *         default="1",
     *         description="Property associated with the data",
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
    public function getPropertyData($id, $key)
    {
        try {
            //if property doesn't exists
            if (!$this->existProperty($id))
                return response()->json(['data' => null, 'message' => "Property doesn't exists", 'status' => 'fail'], 404);

            $propertyData = PropertyData::all()
                ->where('idProperty', $id)
                ->where('keyPropertyData', $key)
                ->first();

            //key doesn't exists
            if (!$propertyData)
                return response()->json(['data' => null, 'message' => "No data for this key", 'status' => 'fail'], 404);

            return response()->json(['data' => $propertyData, 'message' => 'Data successfully retrieved!', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/properties/{id}/data",
     *   summary="Add a data to a specific property",
     *   tags={"PropertyData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the property",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyPropertyData",
     *     in="query",
     *     required=true,
     *     description="Key of the property data",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valuePropertyData",
     *     in="query",
     *     required=true,
     *     description="Value of the property data",
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
     *   @OA\Parameter(
     *     name="idProperty",
     *     in="query",
     *     required=true,
     *     description="Id from property",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Property Data added",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idPropertyData",
     *         default=1,
     *         description="Id of the data of the property",
     *       ),
     *       @OA\Property(
     *         property="keyPropertyData",
     *         default="Some key",
     *         description="Key to add",
     *       ),
     *       @OA\Property(
     *         property="valuePropertyData",
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
     *         property="idProperty",
     *         default="1",
     *         description="Property's ID who this new data is related to",
     *       ),
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
     * )
     */
    public function addPropertyData($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyPropertyData' => 'required|string',
            'valuePropertyData' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            //if created_by and updated_by doesn't exist
            $created_by = User::all()->where('idUser', $request->input('created_by'))->first();
            $updated_by = User::all()->where('idUser', $request->input('updated_by'))->first();
            if (!$this->existProperty($id))
                return response()->json(['data' => null, 'message' => "Unknown Property", 'status' => 'fail'], 404);
            if (!$created_by)
                return response()->json(['data' => null, 'message' => "Creator unknown", 'status' => 'fail'], 404);
            if (!$updated_by)
                return response()->json(['data' => null, 'message' => "User unknown", 'status' => 'fail'], 404);

            //if property data already exists
            $exist = PropertyData::all()
                ->where('keyPropertyData', $request->input('keyPropertyData'))
                ->where('idProperty', $id)
                ->first();
            if ($exist)
                return response()->json(['data' => null, 'message' => "Data already exists", 'status' => 'fail'], 404);

            //creation of the new data
            $propertyData = new PropertyData;
            $propertyData->keyPropertyData = $request->input('keyPropertyData');
            $propertyData->valuePropertyData = $request->input('valuePropertyData');
            $propertyData->created_by = (int)$request->input('created_by');
            $propertyData->updated_by = (int)$request->input('updated_by');
            $propertyData->idProperty = (int)$id;
            $propertyData->save();

            // Return successful response
            return response()->json(['propertyData' => $propertyData, 'message' => 'Property data successfully created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property Data addition failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/properties/{id}/data/{key}",
     *   summary="Update a property data",
     *   tags={"PropertyData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Key of the property related to the data to update",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the property data to update",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyPropertyData",
     *     in="query",
     *     required=false,
     *     description="New keyPropertyData",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valuePropertyData",
     *     in="query",
     *     required=false,
     *     description="New valuePropertyData",
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
     *     name="idProperty",
     *     in="query",
     *     required=false,
     *     description="New idProperty",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Property data updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idPropertyData",
     *         default=1,
     *         description="Id of the property data",
     *       ),
     *       @OA\Property(
     *         property="keyPropertyData",
     *         default="thumbnail",
     *         description="Key of the property data",
     *       ),
     *       @OA\Property(
     *         property="valuePropertyData",
     *         default="any value",
     *         description="Value of the property data",
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
     *         property="idProperty",
     *         default=1,
     *         description="ID of property this data is related to",
     *       ),
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
     * )
     */
    public function updatePropertyData($id, $key, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'keyPropertyData' => 'string',
            'valuePropertyData' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'idProperty' => 'integer',
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

            //if property doesn't exist
            if (!$this->existProperty($id))
                return response()->json(['data' => null, 'message' => "Unknown Property", 'status' => 'fail'], 404);

            //test if the new key already exists
            $newKeyExist = PropertyData::all()
                ->where('idProperty', $id)
                ->where('keyPropertyData', $request->input('keyPropertyData'))
                ->first();
            if ($newKeyExist)
                return response()->json(['message' => 'Data with this key already exists', 'status' => 'fail'], 404);

            // update
            $propertyData = PropertyData::all()
                ->where('idProperty', $id)
                ->where('keyPropertyData', $key)
                ->first();
            if (!$propertyData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            if ($request->input('keyPropertyData') !== null)
                $propertyData->keyPropertyData = $request->input('keyPropertyData');
            if ($request->input('valuePropertyData') !== null)
                $propertyData->valuePropertyData = $request->input('valuePropertyData');
            if ($request->input('created_by') !== null)
                $propertyData->created_by = (int)$request->input('created_by');
            if ($request->input('updated_by') !== null)
                $propertyData->updated_by = (int)$request->input('updated_by');
            if ($request->input('idProperty') !== null)
                $propertyData->idProperty = (int)$request->input('idProperty');

            $propertyData->update();

            // Return successful response
            return response()->json(['data' => $propertyData, 'message' => 'Data successfully updated', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Property data Update Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/properties/{id}/data/{key}",
     *   summary="Delete a property data",
     *   tags={"PropertyData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the property data to delete",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the property data to delete",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Property data deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="keyPropertyData",
     *         default="Any key",
     *         description="Key of the property data",
     *       ),
     *       @OA\Property(
     *         property="valuePropertyData",
     *         default="Any value",
     *         description="Value of the property data",
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
     *         property="idProperty",
     *         default=1,
     *         description="ID of property this data was related to",
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
    public function deletePropertyData($id, $key)
    {
        try {
            $propertyData = PropertyData::all()
                ->where('idProperty', $id)
                ->where('keyPropertyData', $key)
                ->first();

            if (!$propertyData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            $propertyData->delete();

            return response()->json(['data' => $propertyData, 'message' => 'Data successfully deleted', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Data deletion failed!', 'status' => 'fail'], 409);
        }
    }

    private function existProperty($id)
    {
        $property = Property::all()
            ->where('idProperty', $id)
            ->first();
        return (bool) $property;
    }
}
