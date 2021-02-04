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
    public function getVisits(Request $request)
    {
        $visits = Visit::all();

        for ($i = 0; $i < count($visits); $i++) {
            $visit = $visits[$i];

            $visit['data'] = $this->getAllData($visit->idVisit);
        }

        return response()->json(['visits' => $visits], 200);
    }

    /**
     * Get one visit
     *
     * @param  Request  $request
     * @return Response
     */
    public function getVisit($id)
    {
        try {
            $visit = Visit::all()->where('idVisit', $id)->first();
            $visit['data'] = $this->getAllData($id);

            return response()->json(['visit' => $visit, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Visit not found!' . $e->getMessage(), 'status' => 'fail'], 404);
        }
    }
    /**
     * Store a new visit.
     *
     * @param  Request  $request
     * @return Response
     */

    public function addVisit(Request $request)
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

            $visit->save();

            if ($request->input('data') !== null) {
                if (!$this->_addData($visit->idVisit, $request))
                    return response()->json(['message' => 'Visit data not added!', 'status' => 'fail'], 500);
            }

            //return successful response
            return response()->json(['visit' => $visit, 'data' => $this->getAllData($visit->idVisit), 'message' => 'CREATED', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit Data Registration Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * Put visit
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function updateVisit($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'dateVisit' => 'date_format:Y-m-d H:i',
            'keyVisitData' => 'string',
            'valueVisitData' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ]);

        try {
            // On modifie les infos principales du visit
            $visit = Visit::findOrFail($id);
            if ($request->input('dateVisit') !== null)
                $visit->dateVisit = $request->input('dateVisit');
            if ($request->input('keyVisitData') !== null)
                $visit->keyVisitData = $request->input('keyVisitData');
            if ($request->input('valueVisitData') !== null)
                $visit->valueVisitData = $request->input('valueVisitData');
            if ($request->input('created_by') !== null)
                $visit->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $visit->updated_by = $request->input('updated_by');

            $visit->update();

            //maj des data
            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->updateData($visit->idVisit, $key, $value))
                        return response()->json(['message' => 'Visit Update Failed!', 'status' => 'fail'], 500);
                }
            }

            //return successful response
            return response()->json(['visit' => $visit, 'data' => $this->getAllData($visit->idVisit), 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * Delete visit function
     *
     * @param int $id
     * @return Response
     */
    public function deleteVisit($id)
    {
        try {
            $visit = Visit::findOrFail($id);
            $visitData = VisitData::all()->where('idVisit', $id);

            //delete les data
            if ($visitData !== null) {
                if (!$this->deleteData($id))
                    return response()->json(['message' => 'Visit Deletion Failed!', 'status' => 'fail'], 500);
            }

            $visit->delete();

            return response()->json(['visit' => $visit, 'data' => $visitData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    //route
    public function addData($id, Request $request)
    {
        try {
            if (!$this->_addData($id, $request))
                return response()->json(['message' => 'Not all data has been added', 'status' => 'fail'], 409);

            //return successful response
            return response()->json(['data' => $this->getAllData($id), 'message' => 'Data created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit data not added!', 'status' => 'fail'], 409);
        }
    }

    //fonction utilisée par la route et lors de la creation de visit pour ajouter toutes les data
    public function _addData($idVisit, $request)
    {
        $data = (array)json_decode($request->input('data'), true);

        try {
            foreach ($data as $key => $value) {

                $visitData = new VisitData;
                $visitData->keyVisitData = $key;
                $visitData->valueVisitData = $value;
                $visitData->created_by = $request->input('created_by');
                $visitData->updated_by = $request->input('updated_by');
                $visitData->idVisit = $idVisit;

                $visitData->save();
            }

            //return successful response
            return true;
        } catch (\Exception $e) {
            //return error message
            return false;
        }
    }

    public function getAllData($idVisit)
    {
        $data = array();
        foreach (VisitData::all()->where('idVisit', $idVisit) as $value) {
            array_push($data, $value);
        }
        return response()->json($data, 200)->original;
    }

    public function getData($idVisit, $key)
    {
        return response()->json(
            VisitData::all()
                ->where('idVisit', $idVisit)
                ->where('keyVisitData', $key),
            200
        );
    }

    public function updateData($idVisit, $key, $value)
    {
        try {
            $visitData = VisitData::all()
                ->where('idVisit', $idVisit)
                ->where('keyVisitData', $key)
                ->first();

            if ($visitData == null)
                return false;

            $visitData->valueVisitData = $value;
            $visitData->update();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteData($idVisit)
    {
        try {
            $visitData = VisitData::all()->where('idVisit', $idVisit);

            foreach ($visitData as $data) {
                $data->delete();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
