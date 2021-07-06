<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\PropertyData;
use App\Models\User;

class PropertyController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        $this->middleware('auth:api', ['except' => ['getProperties', 'getProperty']]);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/properties",
     *   summary="Return all properties",
     *   tags={"Property Controller"},
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
     *     description="List of properties",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idProperty",
     *         default="Property id",
     *         description="Id of the property",
     *       ),
     *       @OA\Property(
     *         property="typeProperty",
     *         default="Property type",
     *         description="Type of the property",
     *       ),
     *       @OA\Property(
     *         property="priceProperty",
     *         default="Property price",
     *         description="Price of the property",
     *       ),
     *       @OA\Property(
     *         property="zipCodeProperty",
     *         default="Property zipcode",
     *         description="Zipcode of the property",
     *       ),
     *       @OA\Property(
     *         property="cityProperty",
     *         default="Property city",
     *         description="City of the property",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of user who modified this one",
     *       ),
     *     )
     *   )
     * )
     */
    public function getProperties(Request $request)
    {
        // basic filters
        $filterColumns = ['typeProperty', 'minPriceProperty', 'maxPriceProperty', 'zipCodeProperty', 'cityProperty'];

        // beginning of the query
        $query = 'SELECT DISTINCT (p.idProperty) FROM property p INNER JOIN propertyData pd ON (p.idProperty = pd.idProperty)';

        //first filters
        $filters = [];
        foreach ($filterColumns as $column) {
            if ($request->get($column)) {
                //price is handled differently
                if ($column == 'minPriceProperty')
                    $filters[] = "priceProperty >= '" . $request->get($column) . "'";
                if ($column == 'maxPriceProperty')
                    $filters[] = "priceProperty <= '" . $request->get($column) . "'";

                //if the current column isn't about price
                if (!in_array($column, ['minPriceProperty', 'maxPriceProperty']))
                    $filters[] = "$column='" . $request->get($column) . "'";
            }
        }
        $query .= count($filters) > 0 ? ' WHERE ' . implode(' AND ', $filters) : '';

        // building additionnal key/value filters
        $requestedColumns = $request->all();
        $requestedData = [];
        foreach ($requestedColumns as $column => $value) {
            if (!in_array($column, array_merge($filterColumns, ['limit', 'offset']))) {
                $requestedData[] = "(SELECT COUNT(idPropertyData) FROM propertyData WHERE keyPropertyData = '$column' AND valuePropertyData = '$value' AND idProperty = p.idProperty) = 1";
            }
        }

        if (count($filters) == 0) {
            if (count($requestedData) > 0)
                $query .= ' WHERE ';
        } else {
            if (count($requestedData) > 0)
                $query .= ' AND ';
        }
        // add additionnal filters
        $query .= implode(' AND ', $requestedData);


        $queryBeforeLimitOffset = $query;
        //result query
        $resultBeforeLimitOffset = DB::select($queryBeforeLimitOffset);
        //object to array
        $resultBeforeLimitOffset = json_decode(json_encode($resultBeforeLimitOffset), true);


        //limit & offset
        $limit = [];

        if ($request->get('offset'))
            $limit[] = $request->get('offset');
        if ($request->get('limit'))
            $limit[] = $request->get('limit');

        if (count($limit) > 0 && !(count($limit) == 1 && $limit[0] == $request->get('offset')))
            $query .= ' LIMIT ' . implode(',', $limit);

        //result query
        $result = DB::select($query);
        //object to array
        $result = json_decode(json_encode($result), true);

        //foreach id, get property
        $properties = [
            "total" => count($resultBeforeLimitOffset)
        ];
        for ($i = 0; $i < count($result); $i++) {
            $id = $result[$i]['idProperty'];

            //get property
            $properties["properties"][] = Property::all()->where('idProperty', $id)->first();
        }

        //response
        return response()->json($properties, 200);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/properties/{id}",
     *   summary="Return a property",
     *   tags={"Property Controller"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the property to get",
     *     @OA\Schema(
     *       type="integer", default=1
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
     *       description="Property recovery failed!",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="One user",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idProperty",
     *         default="Property id",
     *         description="Id of the property",
     *       ),
     *       @OA\Property(
     *         property="typeProperty",
     *         default="Property type",
     *         description="Type of the property",
     *       ),
     *       @OA\Property(
     *         property="priceProperty",
     *         default="Property price",
     *         description="Price of the property",
     *       ),
     *       @OA\Property(
     *         property="zipCodeProperty",
     *         default="Property zipcode",
     *         description="Zipcode of the property",
     *       ),
     *       @OA\Property(
     *         property="cityProperty",
     *         default="Property city",
     *         description="City of the property",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of user who modified this one",
     *       ),
     *     )
     *   ),
     * )
     */
    public function getProperty($id)
    {
        try {
            $property = Property::all()->where('idProperty', $id)->first();
            if (!$property)
                return response()->json(['message' => 'Property not found!'], 404);

            return response()->json($property, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Property recovery failed!'], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/properties",
     *   summary="Add a property",
     *   tags={"Property Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="typeProperty",
     *     in="query",
     *     required=true,
     *     description="Type of the property to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="priceProperty",
     *     in="query",
     *     required=true,
     *     description="Price of the property to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="zipCodeProperty",
     *     in="query",
     *     required=true,
     *     description="Zipcode of the property to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="cityProperty",
     *     in="query",
     *     required=true,
     *     description="City of the property to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="typeProperty",
     *     in="query",
     *     required=true,
     *     description="Type of the property to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Property creation failed!",
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Property created",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idProperty",
     *         default="Property id",
     *         description="Id of the property",
     *       ),
     *       @OA\Property(
     *         property="typeProperty",
     *         default="Property type",
     *         description="Type of the property",
     *       ),
     *       @OA\Property(
     *         property="priceProperty",
     *         default="Property price",
     *         description="Price of the property",
     *       ),
     *       @OA\Property(
     *         property="zipCodeProperty",
     *         default="Property zipcode",
     *         description="Zipcode of the property",
     *       ),
     *       @OA\Property(
     *         property="cityProperty",
     *         default="Property city",
     *         description="City of the property",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="Id of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of creator",
     *       ),
     *     )
     *   ),
     * )
     */
    public function addProperty(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'typeProperty' => 'required|string',
            'priceProperty' => 'required|string',
            'zipCodeProperty' => 'required|string|min:5|max:5',
            'cityProperty' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {
            $property = new Property;
            $property->typeProperty = $request->input('typeProperty');
            $property->priceProperty = $request->input('priceProperty');
            $property->zipCodeProperty = $request->input('zipCodeProperty');
            $property->cityProperty = $request->input('cityProperty');
            $property->created_by = $request->input('created_by');
            $property->updated_by = $request->input('updated_by');

            $property->save();

            // Return successful response
            return response()->json(['property' => $property, 'message' => 'Property successfully created!', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property Data Registration Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/properties/{id}",
     *   summary="Update a property",
     *   tags={"Property Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the property to update",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="typeProperty",
     *     in="query",
     *     required=true,
     *     description="Type",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="priceProperty",
     *     in="query",
     *     required=true,
     *     description="Price",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="zipCodeProperty",
     *     in="query",
     *     required=true,
     *     description="Zipcode",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="cityProperty",
     *     in="query",
     *     required=true,
     *     description="City",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Property update failed!",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Property updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idProperty",
     *         default="Property id",
     *         description="Id of the property",
     *       ),
     *       @OA\Property(
     *         property="typeProperty",
     *         default="Property type",
     *         description="Type of the property",
     *       ),
     *       @OA\Property(
     *         property="priceProperty",
     *         default="Property price",
     *         description="Price of the property",
     *       ),
     *       @OA\Property(
     *         property="zipCodeProperty",
     *         default="Property zipcode",
     *         description="Zipcode of the property",
     *       ),
     *       @OA\Property(
     *         property="cityProperty",
     *         default="Property city",
     *         description="City of the property",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of user who modified this one",
     *       ),
     *     )
     *   ),
     * )
     */
    public function updateProperty($id, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'typeProperty' => 'string',
            'priceProperty' => 'string',
            'zipCodeProperty' => 'string|min:5|max:5',
            'cityProperty' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ]);

        try {
            // get property
            $property = Property::find($id);

            //test if exists
            if (!$property)
                return response()->json(['property' => null, 'message' => "Property doesn't exists", 'status' => 'success'], 404);

            if ($request->input('typeProperty') !== null)
                $property->typeProperty = $request->input('typeProperty');
            if ($request->input('priceProperty') !== null)
                $property->priceProperty = $request->input('priceProperty');
            if ($request->input('zipCodeProperty') !== null)
                $property->zipCodeProperty = $request->input('zipCodeProperty');
            if ($request->input('cityProperty') !== null)
                $property->cityProperty = $request->input('cityProperty');
            if ($request->input('created_by') !== null) {
                //test if the creator exists
                $exist = User::find($request->input('created_by'));
                if (!$exist)
                    return response()->json(['property' => null, 'message' => 'Unknown creator', 'status' => 'fail'], 404);
                //update if ok
                $property->created_by = $request->input('created_by');
            }
            if ($request->input('updated_by') !== null) {
                //test if the user who did last update exists
                $exist = User::find($request->input('updated_by'));
                if (!$exist)
                    return response()->json(['property' => null, 'message' => 'Unknown user', 'status' => 'fail'], 404);
                //update if ok
                $property->updated_by = $request->input('updated_by');
            }

            $property->update();

            // Return successful response
            return response()->json(['property' => $property, 'message' => 'Property successfully updated', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Property update failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/properties/{id}",
     *   summary="Delete a property",
     *   tags={"Property Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the property to delete",
     *     @OA\Schema(
     *       type="integer", default=1
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
     *       description="Property deletion failed!",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Property deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="typeProperty",
     *         default="Property type",
     *         description="Type of the property",
     *       ),
     *       @OA\Property(
     *         property="priceProperty",
     *         default="Property price",
     *         description="Price of the property",
     *       ),
     *       @OA\Property(
     *         property="zipCodeProperty",
     *         default="Property zipcode",
     *         description="Zipcode of the property",
     *       ),
     *       @OA\Property(
     *         property="cityProperty",
     *         default="Property city",
     *         description="City of the property",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="Id of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of user who did last update",
     *       ),
     *      )
     *   ),
     * )
     */
    public function deleteProperty($id)
    {
        try {
            $property = Property::find($id);

            if (!$property)
                return response()->json(['property' => $property, 'message' => 'Property successfully deleted!', 'status' => 'success'], 200);

            $property->delete();

            return response()->json(['property' => $property, 'message' => 'Property successfully deleted!', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Property deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
