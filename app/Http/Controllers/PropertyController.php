<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\PropertyData;

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
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default="[""]",
     *         description="Property data",
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
            "total" => count($result)
        ];
        for ($i = 0; $i < count($result); $i++) {
            $id = $result[$i]['idProperty'];

            //get property
            $properties["properties"][] = Property::all()->where('idProperty', $id)->first();
            //get property data
            $properties["properties"][$i]['data'] = $this->getAllData($id);
        }

        //response
        return response()->json($properties, 200);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/properties/{id}",
     *   summary="Return a property",
     *   tags={"Property Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the property to get",
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
     *       description="Property not found",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="The property ? doesn't exist",
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
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default={"test":"test"},
     *         description="Property data",
     *       ),
     *     )
     *   ),
     * )
     */
    public function getProperty($id)
    {
        try {
            $property = Property::all()->where('idProperty', $id)->first();
            $property['data'] = $this->getAllData($id);
            return response()->json($property, 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Property not found!' . $e->getMessage()], 404);
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
     *     description="Data of the property to add",
     *     @OA\Schema(
     *       type="string", default={"test":"test"}
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
     *       description="Property data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Property data not added",
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
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default="[""]",
     *         description="Property data",
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
            'data' => 'string',
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

            if ($request->input('data') !== null) {
                if (!$this->_addData($property->idProperty, $request))
                    return response()->json(['message' => 'Property data not added!', 'status' => 'fail'], 500);
            }

            // Return successful response
            return response()->json(['property' => $property, 'data' => $this->getAllData($property->idProperty), 'message' => 'CREATED', 'status' => 'success'], 201);
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
     *       type="number", default="1"
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
     *     description="Data of the property to add",
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
     *       description="Property data not updated",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Property data not updated",
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
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the property last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default="[""]",
     *         description="Property data",
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

            'data' => 'string',
        ]);

        try {
            // Update
            $property = Property::findOrFail($id);
            if ($request->input('typeProperty') !== null)
                $property->typeProperty = $request->input('typeProperty');
            if ($request->input('priceProperty') !== null)
                $property->priceProperty = $request->input('priceProperty');
            if ($request->input('zipCodeProperty') !== null)
                $property->zipCodeProperty = $request->input('zipCodeProperty');
            if ($request->input('cityProperty') !== null)
                $property->cityProperty = $request->input('cityProperty');
            if ($request->input('created_by') !== null)
                $property->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $property->updated_by = $request->input('updated_by');

            $property->update();

            // Update data
            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->updateData($property->idProperty, $key, $value))
                        return response()->json(['message' => 'Property Update Failed!', 'status' => 'fail'], 500);
                }
            }
            // Return successful response
            return response()->json(['property' => $property, 'data' => $this->getAllData($property->idProperty), 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Property Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
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
     *       description="Property data not deleted"
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
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default="[""]",
     *         description="Property data",
     *       )
     *      )
     *   ),
     * )
     */
    public function deleteProperty($id)
    {
        try {
            $property = Property::findOrFail($id);
            $propertyData = $this->getAllData($id);

            // Update data
            if ($propertyData !== null) {
                if (!$this->deleteData($id))
                    return response()->json(['message' => 'Faq Deletion Failed!', 'status' => 'fail'], 500);
            }

            $property->delete();

            return response()->json(['property' => $property, 'data' => $propertyData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Property deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/properties/data/{id}",
     *   summary="Add property data",
     *   tags={"Property Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the property",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     required=true,
     *     description="Data of the property to add",
     *     @OA\Schema(
     *       type="string", default="{'cle':'valeur','deuxiemecle':'deuxiemevaleur'}"
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
     *       description="Data not created",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Property data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Property data not added",
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
     *     description="Property data created",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="data",
     *          default="[""]",
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

            // Return successful response
            return response()->json(['data' => $this->getAllData($id), 'message' => 'Data created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Property data not added!', 'status' => 'fail'], 409);
        }
    }

    // Fonction utilisée par la route et lors de la creation de user pour ajouter toutes les data
    public function _addData($idProperty, $request)
    {
        $data = (array)json_decode($request->input('data'), true);

        try {
            foreach ($data as $key => $value) {
                $propertyData = new PropertyData;
                $propertyData->keyPropertyData = $key;
                $propertyData->valuePropertyData = $value;
                $propertyData->created_by = $request->input('created_by');
                $propertyData->updated_by = $request->input('updated_by');
                $propertyData->idProperty = $idProperty;

                $propertyData->save();
            }

            // Return successful response
            return true;
        } catch (\Exception $e) {
            // Return error message
            return false;
        }
    }

    public function getAllData($idProperty)
    {
        $data = array();
        foreach (PropertyData::all()->where('idProperty', $idProperty) as $value) {
            array_push($data, $value);
        }
        return response()->json($data, 200)->original;
    }

    public function getData($idProperty, $key)
    {
        return response()->json(
            PropertyData::all()
                ->where('idProperty', $idProperty)
                ->where('keyPropertyData', $key),
            200
        );
    }

    public function updateData($idProperty, $key, $value)
    {
        try {
            $propertyData = PropertyData::all()
                ->where('idProperty', $idProperty)
                ->where('keyPropertyData', $key)
                ->first();

            if ($propertyData == null)
                return false;

            $propertyData->valuePropertyData = $value;
            $propertyData->update();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteData($idProperty)
    {
        try {
            $propertyData = PropertyData::all()->where('idProperty', $idProperty);

            foreach ($propertyData as $data) {
                $data->delete();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
