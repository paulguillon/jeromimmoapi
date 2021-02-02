<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;

class PropertyDataController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods without authorization
        $this->middleware('auth:api');
    }

    /**
     * Get all property data
     *
     * @param  Request  $request
     * @return Response
     */
    public function allPropertiesData(Request $request)
    {
        return response()->json(['propertyData' =>  PropertyData::all()], 200);
    }

    /**
     * Get one property data
     *
     * @param  Request  $request
     * @return Response
     */
    public function onePropertyData($id)
    {
        try {
            $propertyData = PropertyData::all()->where('idPropertyData', $id)->first();

            return response()->json(['propertyData' => $propertyData], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Property data not found!' . $e->getMessage()], 404);
        }
    }

    /**
     * Store a new property data.
     *
     * @param  Request  $request
     * @return Response
     */
    public function registerPropertyData(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyPropertyData' => 'required|string',
            'valuePropertyData' => 'required|string',
            'idProperty' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {

            $propertyData = new PropertyData;
            $propertyData->keyPropertyData = $request->input('keyPropertyData');
            $propertyData->valuePropertyData = $request->input('valuePropertyData');
            $propertyData->idProperty = $request->input('idProperty');
            $propertyData->created_by = $request->input('created_by');
            $propertyData->updated_by = $request->input('updated_by');

            $propertyData->save();

            //return successful response
            return response()->json(['propertyData' => $propertyData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property Data Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update property data
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function put($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyPropertyData' => 'required|string',
            'valuePropertyData' => 'required|string',
            'idProperty' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $propertyData = PropertyData::findOrFail($id);
            $propertyData->keyPropertyData = $request->input('keyPropertyData');
            $propertyData->valuePropertyData = $request->input('valuePropertyData');
            $propertyData->idProperty = $request->input('idProperty');
            $propertyData->created_by = $request->input('created_by');
            $propertyData->updated_by = $request->input('updated_by');

            $propertyData->update();

            //return successful response
            return response()->json(['propertyData' => $propertyData, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property Data Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update property patch.
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function patch($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyPropertyData' => 'required|string',
            'valuePropertyData' => 'required|string',
            'idProperty' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $propertyData = PropertyData::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);
            if ($request->input('keyPropertyData') !== null)
                $propertyData->keyPropertyData = $request->input('keyPropertyData');
            if ($request->input('valuePropertyData') !== null)
                $propertyData->valuePropertyData = $request->input('valuePropertyData');
            if ($request->input('idProperty') !== null)
                $propertyData->idProperty = $request->input('idProperty');
            if ($request->input('created_by') !== null)
                $propertyData->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $propertyData->updated_by = $request->input('updated_by');

            $propertyData->update();

            //return successful response
            return response()->json(['propertyData' => $propertyData, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property data Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function delete($id)
    {
        try {
            $propertyData = PropertyData::findOrFail($id);
            $propertyData->delete();

            return response()->json(['propertyData' => $propertyData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property data deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
