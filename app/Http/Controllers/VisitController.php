<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\VisitData;


class VisitController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        $this->middleware('auth:api', ['accept' => []]);
    }

    /**
     * Get all visits
     *
     * @param  Request  $request
     * @return Response
     */
    public function allVisit(Request $request)
    {
        return response()->json(['visit' =>  Visit::all(), 'visitData' => VisitData::all()], 200);
    }

    /**
     * Get one visit
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneVisit($id)
    {
        try {
            $visit = Visit::all()->where('idVisit', $id)->first();

            return response()->json(['visit' => $visit], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Visit not found!' . $e->getMessage()], 404);
        }
    }
    /**
     * Store a new visit.
     *
     * @param  Request  $request
     * @return Response
     */

    public function registerVisit(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'dateVisit' => 'required|date_format:Y-m-d H:i',
            'keyVisitData' => 'string',
            'valueVisitData' => 'string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {

            $visit = new Visit;
            $visit->dateVisit = $request->input('dateVisit');
            $visit->created_by = $request->input('created_by');
            $visit->updated_by = $request->input('updated_by');

            if (!$visit->save())
            return response()->json(['message' => 'Visit Registration Failed !'], 409);

            $visitData = new VisitData;
            $visitData->keyVisitData = $request->input('keyVisitData');
            $visitData->valueVisitData = $request->input('valueVisitData');
            $visitData->idVisit = $visit->idData;
            $visitData->created_by = $request->input('created_by');
            $visitData->updated_by = $request->input('updated_by');
            $visitData->save();
            //return successful response
            return response()->json(['visit' => $visit, 'visitData' => $visitData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit Data Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Put visit
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function put($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'dateVisit' => 'required|date_format:Y-m-d H:i',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {
            $visit = Visit::findOrFail($id);
            $visit->dateVisit = $request->input('dateVisit');
            $visit->created_by = $request->input('created_by');
            $visit->updated_by = $request->input('updated_by');

            $visit->update();

            //return successful response
            return response()->json(['visit' => $visit, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Patch visit
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function patch($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'dateVisit' => 'date_format:Y-m-d H:i',
            'created_by' => 'integer',
            'updated_by' => 'string'
        ]);

        try {
            $visit = Visit::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);

            if ($request->input('dateVisit') !== null)
                $visit->dateVisit = $request->input('dateVisit');
            if ($request->input('created_by') !== null)
                $visit->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $visit->updated_by = $request->input('updated_by');

            $visit->update();

            //return successful response
            return response()->json(['visit' => $visit, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function delete($id)
    {
        try {
            $visit = Visit::findOrFail($id);
            $visit->delete();

            return response()->json(['visit' => $visit, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
