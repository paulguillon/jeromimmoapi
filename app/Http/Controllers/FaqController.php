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
     * Get one faq
     *
     * @param  Request  $request
     * @return Response
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
            return response()->json(['faq' => $faq, 'data' => $this->getAllData($faq->idFaq), 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
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

    public function addData($idFaq, Request $request)
    {
        try {
            if (!$this->_addData($idFaq, $request))
                return response()->json(['message' => 'Not all data has been added', 'status' => 'fail'], 409);

            //return successful response
            return response()->json(['faq' => $this->getAllData($idFaq), 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq data not added!' . $e->getMessage()], 409);
        }
    }

    //fonction utilisée par la route et lors de la creation de user pour ajouter toutes les data
    public function _addData($idFaq, $request)
    {
        $data = (array)json_decode($request->input('data'), true);

        try {
            foreach ($data as $key => $value) {

                $faqData = new FaqData;
                $faqData->keyFaqData = $key;
                $faqData->valueFaqData = $value;
                $faqData->created_by = $request->input('created_by');
                $faqData->updated_by = $request->input('updated_by');
                $faqData->idFaq = $idFaq;

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
