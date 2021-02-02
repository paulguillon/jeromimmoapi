<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
 

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
        return response()->json(['visit' =>  Visit::all()], 200);
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
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {

            $visit = new Visit;
            $visit->dateVisit = $request->input('dateVisit');
            $visit->created_by = $request->input('created_by');
            $visit->updated_by = $request->input('updated_by');

            $visit->save();

            //return successful response
            return response()->json(['visit' => $visit, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update visit
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function updateAll($id, Request $request)
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
     * Update visit patch.
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'dateVisit' => 'required|date_format:Y-m-d H:i',
            'created_by' => 'integer',
            'updated_by' => 'required|string'
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
