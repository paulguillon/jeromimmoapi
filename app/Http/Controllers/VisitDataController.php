<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;

class VisitDataController extends Controller
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
     * Get all Visit data
     *
     * @param  Request  $request
     * @return Response
     */
    public function allVisitData(Request $request)
    {
        return response()->json(['visitData' =>  VisitData::all()], 200);
    }

    /**
     * Get one Visit data
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneVisitData($id)
    {
        try {
            $visitData = VisitData::all()->where('idVisitData', $id)->first();

            return response()->json(['visit' => $visitData], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Visit data not found!' . $e->getMessage()], 404);
        }
    }

    /**
     * Store a new Visit data.
     *
     * @param  Request  $request
     * @return Response
     */
    public function registerVisitData(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyVisitData' => 'required|string',
            'valueVisitData' => 'required|string',
            'idVisit' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {

            $visitData = new VisitData;
            $visitData->keyVisitData = $request->input('keyVisitData');
            $visitData->valueVisitData = $request->input('valueVisitData');
            $visitData->idVisit = $request->input('idVisit');
            $visitData->created_by = $request->input('created_by');
            $visitData->updated_by = $request->input('updated_by');

            $visitData->save();

            //return successful response
            return response()->json(['visitData' => $visitData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit Data Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update Visit data
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function put($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyVisitData' => 'required|string',
            'valueVisitData' => 'required|string',
            'idVisit' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $visitData = VisitData::findOrFail($id);
            $visitData->keyVisitData = $request->input('keyVisitData');
            $visitData->valueVisitData = $request->input('valueVisitData');
            $visitData->idVisit = $request->input('idVisit');
            $visitData->created_by = $request->input('created_by');
            $visitData->updated_by = $request->input('updated_by');

            $visitData->update();

            //return successful response
            return response()->json(['visitData' => $visitData, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit Data Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update Visit patch.
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function patch($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyVisitData' => 'required|string',
            'valueVisitData' => 'required|string',
            'idVisit' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $visitData = VisitData::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);
            if ($request->input('keyVisitData') !== null)
                $visitData->keyVisitData = $request->input('keyVisitData');
            if ($request->input('valueVisitData') !== null)
                $visitData->valueVisitData = $request->input('valueVisitData');
            if ($request->input('idVisit') !== null)
                $visitData->idVisit = $request->input('idVisit');
            if ($request->input('created_by') !== null)
                $visitData->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $visitData->updated_by = $request->input('updated_by');

            $visitData->update();

            //return successful response
            return response()->json(['visitData' => $visitData, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit data Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function delete($id)
    {
        try {
            $visitData = VisitData::findOrFail($id);
            $visitData->delete();

            return response()->json(['visitData' => $visitData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit data deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
