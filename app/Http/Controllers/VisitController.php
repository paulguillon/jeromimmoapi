<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Visit;
class VisitController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        $this->middleware('auth:api', []);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/visits",
     *   summary="Return all visits",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(ref="#/components/parameters/get_request_parameter_limit"),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of visits",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisit",
     *         default=1,
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
     *         default=1,
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of user who modified this one",
     *       ),
     *     )
     *   )
     * )
     */
    public function getVisits()
    {
        $visits = Visit::all();

        return response()->json(['total' => count($visits), 'visits' => $visits, 'message' => "Visits successfully retrieved!", 'status' => 'success'], 200);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/visits/{id}",
     *   summary="Return a visit",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the visit to get",
     *     @OA\Schema(
     *       type="integer", default=1
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
     *       response=409,
     *       description="Visit recovery failed!"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="One visit",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisit",
     *         default=1,
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
     *         default=1,
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of user who modified this one",
     *       ),
     *     )
     *   ),
     * )
     */

    public function getVisit($id)
    {
        try {
            $visit = Visit::find($id);

            if (!$visit)
                return response()->json(['visit' => null, 'message' => "Visit doesn't exists", 'status' => 'success'], 404);

            return response()->json(['visit' => $visit, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Visit not found!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
    /**
     * @OA\Post(
     *   path="/api/v1/visits",
     *   summary="Add a visit",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="dateVisit",
     *     in="query",
     *     required=true,
     *     description="Date of the visit",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number"
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Visit creation failed!",
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Visit created",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisit",
     *         default=1,
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
     *         default=1,
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
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
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {
            $visit = new Visit;
            $visit->dateVisit = $request->input('dateVisit');

            //test if the creator exists
            $exist = User::find($request->input('created_by'));
            if (!$exist)
                return response()->json(['visit' => null, 'message' => 'Unknown creator', 'status' => 'fail'], 404);
            $visit->created_by = (int)$request->input('created_by');

            //test if the user exists
            $exist = User::find($request->input('updated_by'));
            if (!$exist)
                return response()->json(['visit' => null, 'message' => 'Unknown user', 'status' => 'fail'], 404);
            $visit->updated_by = (int)$request->input('updated_by');

            $visit->save();

            //return successful response
            return response()->json(['visit' => $visit, 'message' => 'Visit successfully created!', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit creation failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/visits/{id}",
     *   summary="Update a visit",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the visit to update",
     *     @OA\Schema(
     *       type="number", default=1
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
     *       type="number", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",  
     *     description="ID of the logged user",
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
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Not updated",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Visit updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisit",
     *         default=1,
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
     *         default=1,
     *         description="Id of user who created this visit",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of user who modified this visit",
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
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ]);

        try {
            // get visit
            $visit = Visit::find($id);

            //test if exists
            if (!$visit)
                return response()->json(['visit' => null, 'message' => "Visit doesn't exists", 'status' => 'success'], 404);

            if ($request->input('dateVisit') !== null)
                $visit->dateVisit = $request->input('dateVisit');
            if ($request->input('created_by') !== null) {
                //test if the creator exists
                $exist = User::find($request->input('created_by'));
                if (!$exist)
                    return response()->json(['visit' => null, 'message' => 'Unknown creator', 'status' => 'fail'], 404);
                //update if ok
                $visit->created_by = (int)$request->input('created_by');
            }
            if ($request->input('updated_by') !== null) {
                //test if the creator exists
                $exist = User::find($request->input('updated_by'));
                if (!$exist)
                    return response()->json(['visit' => null, 'message' => 'Unknown user', 'status' => 'fail'], 404);
                //update if ok
                $visit->updated_by = (int)$request->input('updated_by');
            }

            $visit->update();

            //return successful response
            return response()->json(['visit' => $visit, 'message' => 'Visit successfully updated!', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit Update Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/visits/{id}",
     *   summary="Delete a visit",
     *   tags={"Visit Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the visit to delete",
     *     @OA\Schema(
     *       type="number", default=1
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Visit deletion failed!",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Visit deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idVisit",
     *         default=1,
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
     *         default=1,
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of user who modified this one",
     *       ),
     *     )
     *   ),
     * )
     */
    public function deleteVisit($id)
    {
        try {
            $visit = Visit::find($id);

            if (!$visit)
                return response()->json(['visit' => null, 'message' => "Visit doesn't exists", 'status' => 'success'], 404);

            $visit->delete();

            return response()->json(['visit' => $visit, 'message' => 'Visit successfully deleted!', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Visit deletion failed!', 'status' => 'fail'], 409);
        }
    }
}
