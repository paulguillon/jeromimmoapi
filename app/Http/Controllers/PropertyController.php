<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
 

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
    public function allProperties(Request $request)
    {
        return response()->json(['property' =>  Property::all()], 200);
    }

    /**
     * Get one property
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneProperty($id)
    {
        try {
            $property = Property::all()->where('idProperty', $id)->first();

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
    
    public function registerProperty(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'typeProperty' => 'required|string',
            'priceProperty' => 'required|string',
            'zipCodeProperty' => 'required|integer',
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

            //return successful response
            return response()->json(['property' => $property, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Property Registration Failed!' . $e->getMessage()], 409);
        }
    }
}
