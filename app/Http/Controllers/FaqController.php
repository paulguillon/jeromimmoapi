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
        $this->middleware('auth:api', ['accept' => ['registerFaq']]);
    }

    /**
     * Get all faq
     *
     * @param  Request  $request
     * @return Response
     */
    public function getAllFaq(Request $request)
    {
        $faqs = Faq::all();

        for ($i = 0; $i < count($faqs); $i++) {
            $faq = $faqs[$i];

            $faq['data'] = $this->getAllData($faq->idFaq)->original;
        }

        return response()->json(['faq' => $faqs], 200);
    }

    /**
     * Get one faq
     *
     * @param  Request  $request
     * @return Response
     */
    public function getFaq($id)
    {
        try {
            $faq = Faq::all()->where('idFaq', $id)->first();
            $faq['data'] = $this->getAllData($id)->original;
            return response()->json(['faq' => $faq], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Faq not found!' . $e->getMessage()], 404);
        }
    }
    /**
     * Store a new faq.
     *
     * @param  Request  $request
     * @return Response
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
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->addData($faq->idFaq, $key, $value, $request))
                        return response()->json(['message' => 'Faq data not added!', 'status' => 'fail'], 500);
                }
            }
            //return successful response
            return response()->json(['faq' => $faq, 'data' => $this->getAllData($faq->idFaq)->original, 'message' => 'CREATED', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Data Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Patch faq
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
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
            return response()->json(['faq' => $faq, 'data' => $this->getAllData($faq->idFaq)->original, 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * Delete faq function
     *
     * @param int $id
     * @return Response
     */
    public function deleteFaq($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faqData = FaqData::all()->where('idFaq', $id);

            if ($faqData !== null) {
                foreach ($faqData as $key => $value) {
                    if (!$this->deleteData($faq->idFaq, $key))
                        return response()->json(['message' => 'Faq Deletion Failed!', 'status' => 'fail'], 500);
                }
            }
            $faq->delete();

            return response()->json(['faq' => $faq, 'data' => $faqData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function addData($idFaq, $key, $value, $request)
    {
        try {
            $faqData = new FaqData;
            $faqData->keyFaqData = $key;
            $faqData->valueFaqData = $value;
            $faqData->created_by = $request->input('created_by');
            $faqData->updated_by = $request->input('updated_by');
            $faqData->idFaq = $idFaq;

            $faqData->save();

            //return successful response
            return response()->json(['faq' => $faqData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq data not added!' . $e->getMessage()], 409);
        }
    }

    public function getAllData($idFaq)
    {
        return response()->json(FaqData::all()->where('idFaq', $idFaq), 200);
    }

    public function getData($idFaq, $key)
    {
        return response()->json(FaqData::all()->where('idFaq', $idFaq)->where('keyFaqData', $key), 200);
    }

    public function updateData($idFaq, $key, $value)
    {
        try {
            $faqData = FaqData::all()->where('idFaq', $idFaq)->where('keyFaqData', $key)->first();

            if ($faqData == null)
                return false;

            $faqData->valueFaqData = $value;
            $faqData->update();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteData($idFaq, $key)
    {
        try {
            $faqData = FaqData::all()->where('idFaq', $idFaq)->where('keyFaqData', $key)->first();

            if ($faqData == null)
                return false;

            $faqData->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}
