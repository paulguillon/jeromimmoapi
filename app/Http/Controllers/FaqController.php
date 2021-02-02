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
    public function allFaq(Request $request)
    {
        return response()->json(['faq' =>  Faq::all()], 200);
    }

    /**
     * Get one faq
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneFaq($id)
    {
        try {
            $faq = Faq::all()->where('idFaq', $id)->first();

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

    public function registerFaq(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyFaqData' => 'string',
            'valueFaqData' => 'string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {

            $faq = new Faq;
            $faq->created_by = $request->input('created_by');
            $faq->updated_by = $request->input('updated_by');

            if (!$faq->save())
            return response()->json(['message' => 'Faq Registration Failed!'], 409);

            $faqData = new FaqData;
            $faqData->keyFaqData = $request->input('keyFaqData');
            $faqData->valueFaqData = $request->input('valueFaqData');
            $faqData->idFaq = $faq->idFaq;
            $faqData->created_by = $request->input('created_by');
            $faqData->updated_by = $request->input('updated_by');
            $faqData->save();
            //return successful response
            return response()->json(['faq' => $faq, 'faqData' => $faqData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Data Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Put faq
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function put($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $faq = Faq::findOrFail($id);
            $faq->created_by = $request->input('created_by');
            $faq->updated_by = $request->input('updated_by');

            $faq->update();

            //return successful response
            return response()->json(['faq' => $faq, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Patch faq
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function patch($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
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

            //return successful response
            return response()->json(['faq' => $faq, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function delete($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faq->delete();

            return response()->json(['faq' => $faq, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

}
