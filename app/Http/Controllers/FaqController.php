<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\FaqData;


class FaqController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        $this->middleware('auth:api', ['accept' => ['addFaq']]);
    }


    /**
     * @OA\Get(
     *   path="/api/v1/faq",
     *   summary="Return all faq",
     *   tags={"Faq Controller"},
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
     *     description="List of faq",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idfaq",
     *         default="1",
     *         description="id of the faq",
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
     *         description="faq data",
     *       ),
     *     )
     *   )
     * )
     */


    public function getAllFaq(Request $request)
    {
        $faqs = Faq::all();

        for ($i = 0; $i < count($faqs); $i++) {
            $faq = $faqs[$i];

            $faq['data'] = $this->getAllData($faq->idFaq);
        }

        return response()->json(['faq' => $faqs], 200);
    }

     /**
     * @OA\Get(
     *   path="/api/v1/faq/{id}",
     *   summary="Return a faq",
     *   tags={"Faq Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the faq to get",
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
     *       description="faq not found",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="The faq ? doesn't exist",
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
     *     description="One faq",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idFaq",
     *         default="1",
     *         description="Id of the faq",
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
     *         description="faq data",
     *       ),
     *     )
     *   ),
     * )
     */
    public function getFaq($id)
    {
        try {
            $faq = Faq::all()->where('idFaq', $id)->first();
            $faq['data'] = $this->getAllData($id);
            return response()->json(['faq' => $faq], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Faq not found!' . $e->getMessage()], 404);
        }
    }


  /**
     * @OA\Post(
     *   path="/api/v1/faq",
     *   summary="Add a faq",
     *   tags={"Faq Controller"},
     *   security={{ "apiAuth": {} }},
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
     *     description="First name of the faq to add",
     *     @OA\Schema(
     *       type="string", default="{'test':'test','test2':'test2'}"
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
     *       description="faq data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="faq data not added",
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
     *     description="Faq created",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idFaq",
     *         default="1",
     *         description="id of the user",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
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

    public function addFaq(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'data' => 'string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {

            $faq = new Faq;
            $faq->created_by = $request->input('created_by');
            $faq->updated_by = $request->input('updated_by');

            $faq->save();

            if ($request->input('data') !== null) {
                if (!$this->_addData($faq->idFaq, $request))
                    return response()->json(['message' => 'Faq data not added!', 'status' => 'fail'], 500);
            }
            //return successful response
            return response()->json(['faq' => $faq, 'data' => $this->getAllData($faq->idFaq), 'message' => 'CREATED', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Registration Failed!' . $e->getMessage()], 409);
        }
    }

  /**
     * @OA\Patch(
     *   path="/api/v1/faq/{id}",
     *   summary="Update a faq",
     *   tags={"Faq Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="idFaq",
     *     in="path",
     *     required=true,
     *     description="ID of the faq to update",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *     @OA\Property(
     *         property="idFaq",
     *         default="1",
     *         description="Id of the agency",
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
     *       description="Faq data not updated",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Faq data not updated",
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
     *     description="Faq updated",
     *     @OA\JsonContent(
     *     @OA\Property(
     *         property="idFaq",
     *         default="1",
     *         description="Id of the faq",
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
     *         description="Faq data",
     *       ),
     *     )
     *   ),
     * )
     */
    public function updateFaq($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'data' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer'
        ]);

        try {
            $faq = Faq::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);

            if ($request->input('created_by') !== null)
                $faq->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $faq->updated_by = $request->input('updated_by');

            $faq->update();

            //maj des data
            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->updateData($faq->idFaq, $key, $value))
                        return response()->json(['message' => 'Faq Update Failed!', 'status' => 'fail'], 500);
                }
            }
            //return successful response
            return response()->json(['faq' => $faq, 'data' => $this->getAllData($faq->idFaq), 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

   
    /**
     * @OA\Delete(
     *   path="/api/v1/faq/{id}",
     *   summary="Delete a faq",
     *   tags={"Faq Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the faq to delete",
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
     *       description="Faq data not deleted"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Faq deleted",
     *     @OA\JsonContent(
     *     @OA\Property(
     *         property="idFaq",
     *         default="1",
     *         description="Id of the agency",
     *   ),
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
     *         description="Faq data",
     *       ),
     *     )
     *   ),
     * )
     */
    public function deleteFaq($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faqData = $this->getAllData($id);

            //delete data
            if ($faqData !== null) {
                if (!$this->deleteData($id))
                    return response()->json(['message' => 'Faq Deletion Failed!', 'status' => 'fail'], 500);
            }

            $faq->delete();

            return response()->json(['faq' => $faq, 'data' => $faqData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

 /**
     * @OA\Post(
     *   path="/api/v1/faq/data/{id}",
     *   summary="Add faq data",
     *   tags={"Faq Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the faq",
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
     *       type="string", default="{'test':'test'}"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the creator",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
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
     *       description="Faq data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Faq data not added",
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
     *     description="Faq data created",
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
            return response()->json(['faq' => $this->getAllData($id), 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq data not added!' . $e->getMessage()], 409);
        }
    }

    //fonction utilisée par la route et lors de la creation de user pour ajouter toutes les data
    public function _addData($id, $request)
    {
        $data = (array)json_decode($request->input('data'), true);

        try {
            foreach ($data as $key => $value) {

                $faqData = new FaqData;
                $faqData->keyFaqData = $key;
                $faqData->valueFaqData = $value;
                $faqData->created_by = $request->input('created_by');
                $faqData->updated_by = $request->input('updated_by');
                $faqData->idFaq = $id;

                $faqData->save();
            }

            //return successful response
            return true;
        } catch (\Exception $e) {
            //return error message
            return false;
        }
    }

    public function getAllData($idFaq)
    {
        $data = array();
        foreach (FaqData::all()->where('idFaq', $idFaq) as $value) {
            array_push($data, $value);
        }
        return response()->json($data, 200)->original;
    }

    public function getData($idFaq, $key)
    {
        return response()->json(
            FaqData::all()
                ->where('idFaq', $idFaq)
                ->where('keyFaqData', $key),
            200
        );
    }

    public function updateData($idFaq, $key, $value)
    {
        try {
            $faqData = FaqData::all()
                ->where('idFaq', $idFaq)
                ->where('keyFaqData', $key)
                ->first();

            if ($faqData == null)
                return false;

            $faqData->valueFaqData = $value;
            $faqData->update();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteData($idFaq)
    {
        try {
            $faqData = FaqData::all()->where('idFaq', $idFaq);

            foreach ($faqData as $data) {
                $data->delete();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}
