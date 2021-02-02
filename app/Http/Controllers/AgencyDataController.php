<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgencyData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;

class AgencyDataController extends Controller
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
     * Get all Agency data
     *
     * @param  Request  $request
     * @return Response
     */
    public function allAgencyData(Request $request)
    {
        return response()->json(['agencyData' =>  AgencyData::all()], 200);
    }

    /**
     * Get one Agency data
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneAgencyData($id)
    {
        try {
            $agencyData = AgencyData::all()->where('idAgencyData', $id)->first();

            return response()->json(['agency' => $agencyData], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Agency data not found!' . $e->getMessage()], 404);
        }
    }

    /**
     * Store a new Agency data.
     *
     * @param  Request  $request
     * @return Response
     */
    public function registerAgencyData(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyAgencyData' => 'required|string',
            'valueAgencyData' => 'required|string',
            'idAgency' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {

            $agencyData = new AgencyData;
            $agencyData->keyAgencyData = $request->input('keyAgencyData');
            $agencyData->valueAgencyData = $request->input('valueAgencyData');
            $agencyData->idAgency = $request->input('idAgency');
            $agencyData->created_by = $request->input('created_by');
            $agencyData->updated_by = $request->input('updated_by');

            $agencyData->save();

            //return successful response
            return response()->json(['agencyData' => $agencyData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency Data Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update Agency data
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function put($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyAgencyData' => 'required|string',
            'valueAgencyData' => 'required|string',
            'idAgency' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $agencyData = AgencyData::findOrFail($id);
            $agencyData->keyAgencyData = $request->input('keyAgencyData');
            $agencyData->valueAgencyData = $request->input('valueAgencyData');
            $agencyData->idAgency = $request->input('idAgency');
            $agencyData->created_by = $request->input('created_by');
            $agencyData->updated_by = $request->input('updated_by');

            $agencyData->update();

            //return successful response
            return response()->json(['agencyData' => $agencyData, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency Data Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update Agency patch.
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function patch($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyAgencyData' => 'required|string',
            'valueAgencyData' => 'required|string',
            'idAgency' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $agencyData = AgencyData::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);
            if ($request->input('keyAgencyData') !== null)
                $agencyData->keyAgencyData = $request->input('keyAgencyData');
            if ($request->input('valueAgencyData') !== null)
                $agencyData->valueAgencyData = $request->input('valueAgencyData');
            if ($request->input('idAgency') !== null)
                $agencyData->idAgency = $request->input('idAgency');
            if ($request->input('created_by') !== null)
                $agencyData->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $agencyData->updated_by = $request->input('updated_by');

            $agencyData->update();

            //return successful response
            return response()->json(['agencyData' => $agencyData, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency data Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function delete($id)
    {
        try {
            $agencyData = AgencyData::findOrFail($id);
            $agencyData->delete();

            return response()->json(['agencyData' => $agencyData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency data deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
