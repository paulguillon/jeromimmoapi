<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\AgencyData;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        $this->middleware('auth:api', ['accept' => ['registerAgency']]);
    }

    /**
     * Get all agencies
     *
     * @param  Request  $request
     * @return Response
     */
    public function getAgencies(Request $request)
    {
        $agencies = Agency::all();

        for ($i = 0; $i < count($agencies); $i++){
            $agency = $agencies[$i];

            $agency['data'] = $this->getAllData($agency->idAgency)->original;
        }

        return response()->json(['agencies' => $agencies], 200);
    }
    /**
     * Get one agency
     *
     * @param  Request  $request
     * @return Response
     */
    public function getAgency($id)
    {
        try {
            $agency = Agency::all()->where('idAgency', $id)->first();
            $agency['data'] = $this->getAllData($agency->idAgency)->original;
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

    public function addAgency(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameAgency' => 'required|string',
            'zipCodeAgency' => 'required|integer',
            'cityAgency' => 'required|string',
            'keyAgencyData' => 'string',
            'valueAgencyData' => 'string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
            'data' => 'string',
        ]);

        try {
            $agency = new Agency;
            $agency->nameAgency = $request->input('nameAgency');
            $agency->zipCodeAgency = $request->input('zipCodeAgency');
            $agency->cityAgency = $request->input('cityAgency');
            $agency->created_by = $request->input('created_by');
            $agency->updated_by = $request->input('updated_by');

            $agency->save();

            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->addData($agency->idAgency, $key, $value, $request))
                        return response()->json(['message' => 'Agency data not added!', 'status' => 'fail'], 500);

                }
            }

            // Return successful response
            return response()->json(['agency' => $agency, 'message' => 'CREATED', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // return error message
            return response()->json(['message' => 'Agency Registration Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * Update agency
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function updateAgency($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameAgency' => 'string',
            'zipCodeAgency' => 'string|min:5|max:5',
            'cityAgency' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',

            'data' => 'string',
        ]);

        try {
            // Update
            $agency = Agency::findOrFail($id);
            if ($request->input('nameAgency') !== null)
            $agency->nameAgency = $request->input('nameAgency');
            if ($request->input('zipCodeAgency') !== null)
            $agency->zipCodeAgency = $request->input('zipCodeAgency');
            if ($request->input('cityAgency') !== null)
            $agency->cityAgency = $request->input('cityAgency');
            if ($request->input('created_by') !== null)
            $agency->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
            $agency->updated_by = $request->input('updated_by');

            $agency->update();

            // Update data
            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->updateData($agency->idAgency, $key, $value))
                    return response()->json(['message' => 'Agency Update Failed!', 'status' => 'fail'], 500);
                }
            }

            //return successful response
            return response()->json(['agency' => $agency, 'data' => $this->getAllData($agency->idAgency)->original, 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency Update Failed!' . $e->getMessage()], 409);
        }
    }
    /**
     * Delete agency function
     *
     * @param int $id
     * @return Response
     */
    public function deleteAgency($id)
    {
        try {
            $agency = Agency::findOrFail($id);
            $agencyData = AgencyData::all()->where('idAgency', $id);

            // Update data
            if ($agencyData !== null) {
                foreach ($agencyData as $key => $value) {
                    if (!$this->deleteData($agency->idAgency, $key))
                        return response()->json(['message' => 'Agency Deletion Failed!', 'status' => 'fail'], 500);
                }
            }

            $agency->delete();

            return response()->json(['agency' => $agency, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
/**
 * Add data
 *
 * @param [int] $idAgency
 * @param [string] $key
 * @param [string] $value
 * @param request $request
 * @return void
 */
    public function addData($idAgency, $key, $value, $request)
    {
        try {
            $agencyData = new AgencyData;
            $agencyData->keyAgencyData = $key;
            $agencyData->valueAgencyData = $value;
            $agencyData->created_by = $request->input('created_by');
            $agencyData->updated_by = $request->input('updated_by');
            $agencyData->idAgency = $idAgency;

            $agencyData->save();

            //return successful response
            return response()->json(['agency' => $agencyData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency data not added!' . $e->getMessage()], 409);
        }
    }
/**
 * Get all data
 *
 * @param [int] $idAgency
 * @return void
 */
    public function getAllData($idAgency)
    {
        return response()->json(AgencyData::all()->where('idAgency', $idAgency), 200);
    }

    public function getData($idAgency, $key)
    {
        return response()->json(AgencyData::all()->where('idAgency', $idAgency)->where('keyAgencyData', $key), 200);
    }

    public function updateData($idAgency, $key, $value)
    {
        try {
            $agencyData = AgencyData::all()->where('idAgency', $idAgency)->where('keyAgencyData', $key)->first();

            if ($agencyData == null)
                return false;

            $agencyData->valueAgencyData = $value;
            $agencyData->update();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
/**
 * Delete data
 *
 * @param [int] $idAgency
 * @param [string] $key
 * @return void
 */
    public function deleteData($idAgency, $key)
    {
        try {
            $agencyData = AgencyData::all()->where('idAgency', $idAgency)->where('keyAgencyData', $key)->first();

            if ($agencyData == null)
                return false;

            $agencyData->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
