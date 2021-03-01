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
     * @OA\Get(
     *   path="/api/v1/visit",
     *   summary="Return all visits",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(ref="#/components/parameters/get_request_parameter_limit"),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of visits",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisit",
     *         default="1",
     *         description="Id of the visit",
     *       ),
     *       @OA\Property(
     *         property="dateVisit",
     *         default="2020-10-20 20:20:20",
     *         description="Date of the visit",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default="[]",
     *         description="Visit data",
     *       ),
     *     )
     *   )
     * )
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
     * @OA\Get(
     *   path="/api/v1/visit/{id}",
     *   summary="Return a visit",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the visit to get",
     *     @OA\Schema(
     *       type="number", default=1
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Visit not found",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="The visit ? doesn't exist",
     *          description="Message",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="fail",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="One visit",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisit",
     *         default="1",
     *         description="id of the visit",
     *       ),
     *       @OA\Property(
     *         property="dateVisit",
     *         default="2020-10-20 20:20:20",
     *         description="Last name of the user",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default="[]",
     *         description="Visit data",
     *       ),
     *     )
     *   ),
     * )
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
     * @OA\Post(
     *   path="/api/v1/visit",
     *   summary="Add a visit",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="dateVisit",
     *     in="query",
     *     required=true,
     *     description="Date of the visit",
     *     @OA\Schema(
     *       type="string", default="2020-10-20 20:20:20"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     required=true,
     *     description="First name of the visit to add",
     *     @OA\Schema(
     *       type="string", default={"cle":"valeur","deuxiemecle":"deuxiemevaleur"}
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Not created",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Visit data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Visit data not added",
     *          description="Message",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="fail",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Visit created",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisit",
     *         default="1",
     *         description="id of the visit",
     *       ),
     *       @OA\Property(
     *         property="dateVisit",
     *         default="2020-10-20 20:20:20",
     *         description="Last name of the user",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default="[]",
     *         description="User data",
     *       ),
     *     )
     *   ),
     * )
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
     * @OA\Patch(
     *   path="/api/v1/visit/{id}",
     *   summary="Update a visit",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the visit to update",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="dateVisit",
     *     in="query",
     *     description="Date of the visit",
     *     @OA\Schema(
     *       type="string", default="2020-10-20 20:20:20"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",  
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     description="data to add",
     *     @OA\Schema(
     *       type="string", default={"cle":"valeur","deuxiemecle":"deuxiemevaleur"}
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Not updated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Visit data not updated",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Visit data not updated",
     *          description="Message",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="fail",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Visit updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisit",
     *         default="1",
     *         description="id of the visit",
     *       ),
     *       @OA\Property(
     *         property="dateVisit",
     *         default="2020-12-20 20:20:20",
     *         description="Date of the visit",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this visit",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this visit",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default="[]",
     *         description="Visit data",
     *       ),
     *     )
     *   ),
     * )
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
     * @OA\Delete(
     *   path="/api/v1/visit/{id}",
     *   summary="Delete a visit",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the visit to delete",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Not deleted",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Visit data not deleted"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Visit deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisit",
     *         default="1",
     *         description="id of the visit",
     *       ),
     *       @OA\Property(
     *         property="dateVisit",
     *         default="2020-12-20 20:20",
     *         description="Date of the visit",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default="[]",
     *         description="User data",
     *       ),
     *     )
     *   ),
     * )
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
    /**
     * @OA\Post(
     *   path="/api/v1/visit/data/{id}",
     *   summary="Add visit data",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the visit",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     required=true,
     *     description="data to add",
     *     @OA\Schema(
     *       type="string", default={"cle":"valeur","deuxiemecle":"deuxiemevaleur"}
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data not created",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Visit data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="User data not added",
     *          description="Message",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="fail",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Visit data created",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="data",
     *          default="[]",
     *          description="data",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="success",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     * )
     */
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
