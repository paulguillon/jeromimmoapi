<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Faq;
use App\Models\FaqData;

class FaqDataController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        $this->middleware('auth:api', ['except' => ['getFaqData', 'getAllData']]);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/faq/{id}/data",
     *   summary="Return all data of specific faq",
     *   tags={"FaqData Controller"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the faq",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data could not be retrieved"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idFaqData",
     *         default=1,
     *         description="Id of the faq data",
     *       ),
     *       @OA\Property(
     *         property="keyFaqData",
     *         default="Any key",
     *         description="Key of the faq data",
     *       ),
     *       @OA\Property(
     *         property="valueFaqData",
     *         default="Any value",
     *         description="Value of the faq data",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="ID of user who did the last update",
     *       ),
     *       @OA\Property(
     *         property="idFaq",
     *         default=1,
     *         description="ID of the faq that this data is related to",
     *       ),
     *     )
     *   )
     * )
     */
    public function getAllData($id)
    {
        try {
            //if faq doesn't exists
            if (!$this->existFaq($id))
                return response()->json(['data' => null, 'message' => "Faq doesn't exists", 'status' => 'fail'], 404);

            $data = array_values(FaqData::all()->where('idFaq', $id)->toArray());

            return response()->json(['total' => count($data), 'data' => $data, 'message' => 'Faq data successfully retrieved', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/v1/faq/{id}/data/{key}",
     *   summary="Return specific data of the specified faq",
     *   tags={"FaqData Controller"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the concerned faq",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="key of the faq to get",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No data for this key"
     *   ),
     *   @OA\Response(
     *     response=409,
     *     description="Server error"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Data not found",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         default="Data doesn't exist",
     *         description="Message",
     *       ),
     *       @OA\Property(
     *         property="status",
     *         default="fail",
     *         description="Status",
     *       ),
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Requested data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idFaqData",
     *         default="1",
     *         description="key of the faq",
     *       ),
     *       @OA\Property(
     *         property="keyFaqData",
     *         default="key",
     *         description="key of the faq",
     *       ),
     *       @OA\Property(
     *         property="valueFaqData",
     *         default="Any value",
     *         description="Value of the faq",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Who did the last update",
     *       ),
     *       @OA\Property(
     *         property="idFaq",
     *         default="1",
     *         description="Faq associated with the data",
     *       ),
     *     )
     *   ),
     * )
     */
    public function getFaqData($id, $key)
    {
        try {
            //if faq doesn't exists
            if (!$this->existFaq($id))
                return response()->json(['data' => null, 'message' => "Faq doesn't exists", 'status' => 'fail'], 404);

            $faqData = FaqData::all()
                ->where('idFaq', $id)
                ->where('keyFaqData', $key)
                ->first();

            //key doesn't exists
            if (!$faqData)
                return response()->json(['data' => null, 'message' => "No data for this key", 'status' => 'fail'], 404);

            return response()->json(['data' => $faqData, 'message' => 'Data successfully retrieved!', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/faq/{id}/data",
     *   summary="Add a data to a specific faq",
     *   tags={"FaqData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the faq",
     *     @OA\Schema(
     *       type="integer", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyFaqData",
     *     in="query",
     *     required=true,
     *     description="Key of the faq data",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueFaqData",
     *     in="query",
     *     required=true,
     *     description="Value of the faq data",
     *     @OA\Schema(
     *       type="string", default="Any value"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the creator",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the last user who changed this line",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Unknown Property"
     *   ),
     *   @OA\Response(
     *     response=409,
     *     description="Data addition failed!",
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Data added",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idFaqData",
     *         default=1,
     *         description="Id of the faq",
     *       ),
     *       @OA\Property(
     *         property="keyFaqData",
     *         default="Some key",
     *         description="Key to add",
     *       ),
     *       @OA\Property(
     *         property="valueFaqData",
     *         default="Any value",
     *         description="Value of the key to add",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="ID of user who has updated",
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default="1",
     *         description="User's id who this new data is related to",
     *       ),
     *     )
     *   ),
     * )
     */
    public function addFaqData($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyFaqData' => 'required|string',
            'valueFaqData' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            //if faq or users doesn't exist
            $created_by = User::all()->where('idUser', $request->input('created_by'))->first();
            $updated_by = User::all()->where('idUser', $request->input('updated_by'))->first();
            if (!$this->existFaq($id))
                return response()->json(['data' => null, 'message' => "Unknown Property", 'status' => 'fail'], 404);
            if (!$created_by)
                return response()->json(['data' => null, 'message' => "Creator unknown", 'status' => 'fail'], 404);
            if (!$updated_by)
                return response()->json(['data' => null, 'message' => "User unknown", 'status' => 'fail'], 404);

            //if faq data already exists
            $exist = FaqData::all()
                ->where('keyFaqData', $request->input('keyFaqData'))
                ->where('idFaqData', $id)
                ->first();
            if ($exist)
                return response()->json(['data' => null, 'message' => "Data already exists", 'status' => 'fail'], 404);

            //creation of the new data
            $faqData = new FaqData;
            $faqData->keyFaqData = $request->input('keyFaqData');
            $faqData->valueFaqData = $request->input('valueFaqData');
            $faqData->created_by = (int)$request->input('created_by');
            $faqData->updated_by = (int)$request->input('updated_by');
            $faqData->save();

            // Return successful response
            return response()->json(['faqData' => $faqData, 'message' => 'Faq data successfully created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Data addition failed!', $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/faq/{id}/data/{key}",
     *   summary="Update an faq data",
     *   tags={"FaqData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Key of the faq related to the data to update",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the faq data to update",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyFaqData",
     *     in="query",
     *     required=false,
     *     description="New keyFaqData",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueFaqData",
     *     in="query",
     *     required=false,
     *     description="New valueFaqData",
     *     @OA\Schema(
     *       type="string", default="any value"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=false,
     *     description="New creator",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=false,
     *     description="New modifier",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data update failed",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Faq data updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idFaqData",
     *         default=1,
     *         description="Id of the faq data",
     *       ),
     *       @OA\Property(
     *         property="keyFaqData",
     *         default="thumbnail",
     *         description="Key of the faq data",
     *       ),
     *       @OA\Property(
     *         property="valueFaqData",
     *         default="any value",
     *         description="Value of the faq data",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="ID of user that modifier this data",
     *       ),
     *       @OA\Property(
     *         property="idFaq",
     *         default=1,
     *         description="ID of faq this data is related to",
     *       ),
     *     )
     *   ),
     * )
     */
    public function updateFaqData($id, $key, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'keyFaqData' => 'string',
            'valueFaqData' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'idFaq' => 'integer',
        ]);

        try {
            //if users doesn't exist
            if ($request->input('created_by')) {
                $created_by = User::all()->where('idUser', $request->input('created_by'))->first();
                if (empty($created_by))
                    return response()->json(['data' => null, 'message' => "Creator unknown", 'status' => 'fail'], 404);
            }
            if ($request->input('updated_by')) {
                $updated_by = User::all()->where('idUser', $request->input('updated_by'))->first();
                if (empty($updated_by))
                    return response()->json(['data' => null, 'message' => "User unknown", 'status' => 'fail'], 404);
            }

            //if faq doesn't exists
            if (!$this->existFaq($id))
                return response()->json(['data' => null, 'message' => "Unknown Property", 'status' => 'fail'], 404);
                
            // update
            $faqData = FaqData::all()
                ->where('idFaq', $id)
                ->where('keyFaqData', $key)
                ->first();
            if (!$faqData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            if ($request->input('keyFaqData') !== null)
                $faqData->keyFaqData = $request->input('keyFaqData');
            if ($request->input('valueFaqData') !== null)
                $faqData->valueFaqData = $request->input('valueFaqData');
            if ($request->input('created_by') !== null)
                $faqData->created_by = (int)$request->input('created_by');
            if ($request->input('updated_by') !== null)
                $faqData->updated_by = (int)$request->input('updated_by');
            if ($request->input('idFaq') !== null)
                $faqData->idFaq = (int)$request->input('idFaq');

            $faqData->update();

            // Return successful response
            return response()->json(['data' => $faqData, 'message' => 'Data successfully updated', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Faq data Update Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/faq/{id}/data/{key}",
     *   summary="Delete an faq data",
     *   tags={"FaqData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the faq data to delete",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the faq data to delete",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="No data for this key"
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data deletion failed!",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Property data deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="keyFaqData",
     *         default="Any key",
     *         description="Key of the faq data",
     *       ),
     *       @OA\Property(
     *         property="valueFaqData",
     *         default="any value",
     *         description="Value of the faq data",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="ID of user who deleted this data",
     *       ),
     *       @OA\Property(
     *         property="idFaq",
     *         default=1,
     *         description="ID of faq this data was related to",
     *       )
     *      )
     *   ),
     * )
     */
    public function deleteFaqData($id, $key)
    {
        try {
            $faqData = FaqData::all()
                ->where('idFaq', $id)
                ->where('keyFaqData', $key)
                ->first();

            if (!$faqData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            $faqData->delete();

            return response()->json(['data' => $faqData, 'message' => 'Data successfully deleted', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Data deletion failed!', 'status' => 'fail'], 409);
        }
    }

    private function existFaq($id)
    {
        $faq = Faq::all()
            ->where('idFaq', $id)
            ->first();
        return (bool) $faq;
    }
}
