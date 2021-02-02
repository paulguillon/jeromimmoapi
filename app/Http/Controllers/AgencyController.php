<?php

namespace App\Http\Controllers;

use App\Models\Agency;
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
     * Get all agency
     *
     * @param  Request  $request
     * @return Response
     */
    public function allAgency(Request $request)
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
     * Create a new agency.
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

    /**
     * Update agency
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function put($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameAgency' => 'required|string',
            'zipCodeAgency' => 'required|string|min:5|max:5',
            'cityAgency' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $agency = Agency::findOrFail($id);
            $agency->nameAgency = $request->input('nameAgency');
            $agency->zipCodeAgency = $request->input('zipCodeAgency');
            $agency->cityAgency = $request->input('cityAgency');
            $agency->created_by = $request->input('created_by');
            $agency->updated_by = $request->input('updated_by');

            $agency->update();

            //return successful response
            return response()->json(['agency' => $agency, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update agency patch.
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function patch($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameAgency' => 'string',
            'zipCodeAgency' => 'string|min:5|max:5',
            'cityAgency' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ]);

        try {
            $agency = Agency::findOrFail($id);

            if (in_array(null or '', $request->all())) {
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);
            }

            if ($request->input('nameAgency') !== null) {
                $agency->nameAgency = $request->input('nameAgency');
            }
            if ($request->input('zipCodeAgency') !== null) {
                $agency->zipCodeAgency = $request->input('zipCodeAgency');
            }
            if ($request->input('cityAgency') !== null) {
                $agency->cityAgency = $request->input('cityAgency');
            }
            if ($request->input('created_by') !== null) {
                $agency->created_by = $request->input('created_by');
            }
            if ($request->input('updated_by') !== null) {
                $agency->updated_by = $request->input('updated_by');
            }

            $agency->update();

            //return successful response
            return response()->json(['agency' => $agency, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * Delete agency function
     *
     * @param [integer] $id
     * @return void
     */
    public function delete($id)
    {
        try {
            $agency = Agency::findOrFail($id);
            $agency->delete();

            return response()->json(['agency' => $agency, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Agency deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
