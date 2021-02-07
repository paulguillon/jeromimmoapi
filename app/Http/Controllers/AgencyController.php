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
     * @OA\Get(
     *   path="/api/v1/agency",
     *   summary="Return all agencies",
     *   tags={"Agency Controller"},
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
     *     description="List of agencies",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="id of the agency",
     *       ),
     *       @OA\Property(
     *         property="nameAgency",
     *         default="name",
     *         description="Name of the agency",
     *       ),
     *       @OA\Property(
     *         property="zipCodeAgency",
     *         default="zip code",
     *         description="Zip code of the agency",
     *       ),
     *       @OA\Property(
     *         property="cityAgency",
     *         default="city",
     *         description="City of the agency",
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
     *         description="Agency data",
     *       ),
     *     )
     *   )
     * )
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
     * @OA\Get(
     *   path="/api/v1/agency/{id}",
     *   summary="Return a agency",
     *   tags={"Agency Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the agency to get",
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
     *       description="Agency not found",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="The agency ? doesn't exist",
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
     *     description="One agency",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Id of the agency",
     *       ),
     *       @OA\Property(
     *         property="nameAgency",
     *         default="name",
     *         description="Name of the agency",
     *       ),
     *       @OA\Property(
     *         property="zipCodeAgency",
     *         default="zip code",
     *         description="Zip code of the agency",
     *       ),
     *       @OA\Property(
     *         property="cityAgency",
     *         default="city",
     *         description="City of the agency",
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
     *         description="Agency data",
     *       ),
     *     )
     *   ),
     * )
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
     * @OA\Post(
     *   path="/api/v1/agency",
     *   summary="Add a agency",
     *   tags={"Agency Controller"},
     *   @OA\Parameter(
     *     name="nameAgency",
     *     in="query",
     *     required=true,
     *     description="Name of the agency to add",
     *     @OA\Schema(
     *       type="string", default="Name agency"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="zipCodeAgency",
     *     in="query",
     *     required=true,
     *     description="Zip code of the agency to add",
     *     @OA\Schema(
     *       type="string", default="Zip code"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="cityAgency",
     *     in="query",
     *     required=true,
     *     description="City of the agency to add",
     *     @OA\Schema(
     *       type="string", default="City"
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
     *     description="Name of the data agency to add",
     *     @OA\Schema(
     *       type="string", default="{'cle':'valeur','deuxiemecle':'deuxiemevaleur'}"
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
     *       description="Agency data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Agency data not added",
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
     *     description="Agency created",
     *     @OA\JsonContent(
     *     @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Id of the agency",
     *       ),
     *       @OA\Property(
     *         property="nameAgency",
     *         default="name",
     *         description="Name of the agency",
     *       ),
     *       @OA\Property(
     *         property="zipCodeAgency",
     *         default="zipCode",
     *         description="Zip Code of the Agency",
     *       ),
     *       @OA\Property(
     *         property="cityAgency",
     *         default="city Agency",
     *         description="City of the agency",
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
     *         description="Agency data",
     *       ),
     *     )
     *   ),
     * )
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
     * @OA\Patch(
     *   path="/api/v1/agency/{id}",
     *   summary="Update a agency",
     *   tags={"Agency Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the agency to update",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *     @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Id of the agency",
     *   ),
     *   @OA\Parameter(
     *     name="nameAgency",
     *     in="query",
     *     description="Name of the agency to add",
     *     @OA\Schema(
     *       type="string", default="Name"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="zipCodeAgency",
     *     in="query",
     *     description="Zip Code of the agency to add",
     *     @OA\Schema(
     *       type="string", default="Zip Code"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="cityAgency",
     *     in="query",
     *     description="City of the agency to add",
     *     @OA\Schema(
     *       type="string", default="City"
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
     *     description="Data to add",
     *     @OA\Schema(
     *       type="string", default="{'cle':'valeur','deuxiemecle':'deuxiemevaleur'}"
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
     *       description="Agency data not updated",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Agency data not updated",
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
     *     description="Agency updated",
     *     @OA\JsonContent(
     *     @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Id of the agency",
     *       ),
     *       @OA\Property(
     *         property="nameagency",
     *         default="Name",
     *         description="Name of the agency",
     *       ),
     *       @OA\Property(
     *         property="zipCodeAgency",
     *         default="ZipCode",
     *         description="sip code of the agency",
     *       ),
     *       @OA\Property(
     *         property="city",
     *         default="CityLe Havre",
     *         description="City of the agency",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Id of user who created this one",
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
     *         description="Agency data",
     *       ),
     *     )
     *   ),
     * )
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
     * @OA\Delete(
     *   path="/api/v1/agency/{id}",
     *   summary="Delete a agency",
     *   tags={"Agency Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the agency to delete",
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
     *       description="Agency data not deleted"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Agency deleted",
     *     @OA\JsonContent(
     *     @OA\Property(
     *         property="idAgency",
     *         default="1",
     *         description="Id of the agency",
     *   ),
     *       @OA\Property(
     *         property="nameAgency",
     *         default="Name",
     *         description="Name of the agency",
     *       ),
     *       @OA\Property(
     *         property="zipCodeAgency",
     *         default="ZipCode",
     *         description="zipCode of the agency",
     *       ),
     *       @OA\Property(
     *         property="cityAgency",
     *         default="City",
     *         description="City of the agency",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of agency who created this one",
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
     *         description="Agency data",
     *       ),
     *     )
     *   ),
     * )
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
    /**
     * @OA\Post(
     *   path="/api/v1/agency/data/{id}",
     *   summary="Add agency data",
     *   tags={"Agency Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the agency",
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
     *       type="string", default="{}"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     description="ID of the creator",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     description="ID of the updator",
     *     @OA\Schema(
     *       type="number", default="1"
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
     *       description="Agency data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Agency data not added",
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
     *     description="Agency data created",
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
