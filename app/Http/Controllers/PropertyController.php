<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $this->middleware('auth:api', ['accept' => ['registerProperty']]);
    }

    /**
     * Get all properties
     *
     * @param  Request  $request
     * @return Response
     */
    public function getProperties(Request $request)
    {
        $properties = Property::all();

        for ($i = 0; $i < count($properties); $i++) {
            $property = $properties[$i];

            $property['data'] = $this->getAllData($property->idProperty)->original;
        }
        return response()->json(['properties' => $properties], 200);
    }

    /**
     * Get one property
     *
     * @param  Request  $request
     * @return Response
     */
    public function getProperty($id)
    {
        try {
            $property = Property::all()->where('idProperty', $id)->first();
            $property['data'] = $this->getAllData($property->idProperty)->original;
            return response()->json(['property' => $property], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'property not found!' . $e->getMessage()], 404);
        }
    }
    /**
     * Store a new property.
     *
     * @param  Request  $request
     * @return Response
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
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->addData($property->idProperty, $key, $value, $request))
                        return response()->json(['message' => 'Property data not added!', 'status' => 'fail'], 500);
                }
            }
            //return successful response
            return response()->json(['property' => $property, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property Data Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Patch property
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function updateProperty($id, Request $request)
    {
        //validate incoming request
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
            // On modifie les infos principales du bien
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

            //maj des data
            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->updateData($property->idProperty, $key, $value))
                        return response()->json(['message' => 'Property Update Failed!', 'status' => 'fail'], 500);
                }
            }
            //return successful response
            return response()->json(['proporty' => $property, 'data' => $this->getAllData($property->idProperty)->original, 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

/**
     * Delete property function
     *
     * @param int $id
     * @return Response
     */
    public function deleteProperty($id)
    {
        try {
            $property = Property::findOrFail($id);
            $propertyData = PropertyData::all()->where('idProperty', $id);

            //maj des data
            if ($propertyData !== null) {
                foreach ($propertyData as $key => $value) {
                    if (!$this->deleteData($property->idProperty, $key))
                    return response()->json(['message' => 'Property Deletion Failed!','status' => 'fail'], 500);
                }
            }
            $property->delete();

            return response()->json(['property' => $property, 'data' => $propertyData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function addData($idProperty, $key, $value, $request)
    {
        try {
            $propertyData = new PropertyData;
            $propertyData->keyPropertyData = $key;
            $propertyData->valuePropertyData = $value;
            $propertyData->created_by = $request->input('created_by');
            $propertyData->updated_by = $request->input('updated_by');
            $propertyData->idProperty = $idProperty;

            $propertyData->save();

            //return successful response
            return response()->json(['property' => $propertyData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property data not added!' . $e->getMessage()], 409);
        }
    }

    public function getAllData($idProperty)
    {
        return response()->json(PropertyData::all()->where('idProperty', $idProperty), 200);
    }

    public function getData($idProperty, $key)
    {
        return response()->json(PropertyData::all()->where('idProperty', $idProperty)->where('keyPropertyData', $key), 200);
    }

    public function updateData($idProperty, $key, $value)
    {
        try {
            $propertyData = PropertyData::all()->where('idProperty', $idProperty)->where('keyPropertyData', $key)->first();

            if ($propertyData == null)
                return false;

            $propertyData->valuePropertyData = $value;

            $propertyData->update();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteData($idProperty, $key)
    {
        try {
            $propertyData = PropertyData::all()->where('idProperty', $idProperty)->where('keyPropertyData', $key)->first();

            if ($propertyData == null)
                return false;

            $propertyData->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
