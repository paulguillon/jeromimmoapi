<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;
 

class AgencyController extends Controller
{
    /**
     * Get all agency
     *
     * @param  Request  $request
     * @return Response
     */
    public function allProperties(Request $request)
    {
        return response()->json(['agency' =>  Agency::all()], 200);
    }

    /**
     * Get one agency
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneAgency($id)
    {
        try {
            $agency = Agency::all()->where('idAgency', $id)->first();

            return response()->json(['agency' => $agency], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'agency not found!' . $e->getMessage()], 404);
        }
    }
    /**
     * Store a new agency.
     *
     * @param  Request  $request
     * @return Response
     */
    
    public function registerAgency(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameAgency' => 'required|string',
            'zipCodeAgency' => 'required|integer',
            'cityAgency' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {

            $agency = new Agency;
            $agency->nameAgency = $request->input('nameAgency');
            $agency->zipCodeAgency = $request->input('zipCodeAgency');
            $agency->cityAgency = $request->input('cityAgency');
            $agency->created_by = $request->input('created_by');
            $agency->updated_by = $request->input('updated_by');

            $agency->save();

            //return successful response
            return response()->json(['agency' => $agency, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency Registration Failed!' . $e->getMessage()], 409);
        }
    }
}
