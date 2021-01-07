<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
 

class VisitController extends Controller
{
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
            'dateVisit' => 'required|datetime',
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
}
