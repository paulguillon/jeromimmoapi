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

        for ($i = 0; $i < count($agencies); $i++) {
            $agency = $agencies[$i];

            $agency['data'] = $this->getAllData($agency->idAgency);
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
            $agency['data'] = $this->getAllData($id);
            return response()->json(['agency' => $agency], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Agency not found!' . $e->getMessage()], 404);
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
                if (!$this->_addData($agency->idAgency, $request))
                    return response()->json(['message' => 'Agency data not added!', 'status' => 'fail'], 500);
            }

            // Return successful response
            return response()->json(['agency' => $agency, 'data' => $this->getAllData($agency->idAgency), 'message' => 'CREATED', 'status' => 'success'], 201);
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
            return response()->json(['agency' => $agency, 'data' => $this->getAllData($agency->idAgency), 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
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
            $agencyData = $this->getAllData($id);

            // Update data
            if ($agencyData !== null) {
                if (!$this->deleteData($id))
                    return response()->json(['message' => 'Faq Deletion Failed!', 'status' => 'fail'], 500);
            }

            $agency->delete();

            return response()->json(['agency' => $agency, 'data' => $agencyData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
    //route
    public function addData($id, Request $request)
    {
        try {
            if (!$this->_addData($id, $request))
                return response()->json(['message' => 'Not all data has been added', 'status' => 'fail'], 409);

            // Return successful response
            return response()->json(['data' => $this->getAllData($id), 'message' => 'Data created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Agency data not added!', 'status' => 'fail'], 409);
        }
    }
    //fonction utilisée par la route et lors de la creation de agency pour ajouter toutes les data
    public function _addData($idAgency, $request)
    {
        $data = (array)json_decode($request->input('data'), true);

        try {
            foreach ($data as $key => $value) {

                $agencyData = new AgencyData;
                $agencyData->keyAgencyData = $key;
                $agencyData->valueAgencyData = $value;
                $agencyData->created_by = $request->input('created_by');
                $agencyData->updated_by = $request->input('updated_by');
                $agencyData->idAgency = $idAgency;

                $agencyData->save();
            }
            // Return successful response
            return true;
        } catch (\Exception $e) {
            // Return error message
            return false;
        }
    }

    public function getAllData($idAgency)
    {
        $data = array();
        foreach (AgencyData::all()->where('idAgency', $idAgency) as $value) {
            array_push($data, $value);
        }
        return response()->json($data, 200)->original;
    }

    public function getData($idAgency, $key)
    {
        return response()->json(
            AgencyData::all()
                ->where('idAgency', $idAgency)
                ->where('keyAgencyData', $key),
            200
        );
    }

    public function updateData($idAgency, $key, $value)
    {
        try {
            $agencyData = AgencyData::all()
            ->where('idAgency', $idAgency)
            ->where('keyAgencyData', $key)
            ->first();

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
    public function deleteData($idAgency)
    {
        try {
            $agencyData = AgencyData::all()->where('idAgency', $idAgency);

            foreach ($agencyData as $data) {
                $data->delete();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
