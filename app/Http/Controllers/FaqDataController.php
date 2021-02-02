<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaqData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;

class FaqDataController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods without authorization
        $this->middleware('auth:api', ['except' => ['allFaqData', 'oneFaqData']]);
    }

    /**
     * Get all Faq data
     *
     * @param  Request  $request
     * @return Response
     */
    public function allFaqData(Request $request)
    {
        return response()->json(['faqData' =>  FaqData::all()], 200);
    }

    /**
     * Get one Faq data
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneFaqData($id)
    {
        try {
            $faqData = FaqData::all()->where('idFaqData', $id)->first();

            return response()->json(['faq' => $faqData], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Faq data not found!' . $e->getMessage()], 404);
        }
    }

    /**
     * Store a new Faq data.
     *
     * @param  Request  $request
     * @return Response
     */
    public function registerFaqData(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyFaqData' => 'required|string',
            'valueFaqData' => 'required|string',
            'idFaq' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {

            $faqData = new FaqData;
            $faqData->keyFaqData = $request->input('keyFaqData');
            $faqData->valueFaqData = $request->input('valueFaqData');
            $faqData->idFaq = $request->input('idFaq');
            $faqData->created_by = $request->input('created_by');
            $faqData->updated_by = $request->input('updated_by');

            $faqData->save();

            //return successful response
            return response()->json(['faqData' => $faqData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Data Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update Faq data
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function put($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyFaqData' => 'required|string',
            'valueFaqData' => 'required|string',
            'idFaq' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $faqData = FaqData::findOrFail($id);
            $faqData->keyFaqData = $request->input('keyFaqData');
            $faqData->valueFaqData = $request->input('valueFaqData');
            $faqData->idFaq = $request->input('idFaq');
            $faqData->created_by = $request->input('created_by');
            $faqData->updated_by = $request->input('updated_by');

            $faqData->update();

            //return successful response
            return response()->json(['faqData' => $faqData, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Data Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update Faq patch.
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function patch($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyFaqData' => 'required|string',
            'valueFaqData' => 'required|string',
            'idFaq' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $faqData = FaqData::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);
            if ($request->input('keyFaqData') !== null)
                $faqData->keyFaqData = $request->input('keyFaqData');
            if ($request->input('valueFaqData') !== null)
                $faqData->valueFaqData = $request->input('valueFaqData');
            if ($request->input('idFaq') !== null)
                $faqData->idFaq = $request->input('idFaq');
            if ($request->input('created_by') !== null)
                $faqData->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $faqData->updated_by = $request->input('updated_by');

            $faqData->update();

            //return successful response
            return response()->json(['faqData' => $faqData, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq data Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function delete($id)
    {
        try {
            $faqData = FaqData::findOrFail($id);
            $faqData->delete();

            return response()->json(['faqData' => $faqData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq data deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
