<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
 

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
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {

            $faq = new Faq;
            $faq->created_by = $request->input('created_by');
            $faq->updated_by = $request->input('updated_by');

            $faq->save();

            //return successful response
            return response()->json(['faq' => $faq, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Faq Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update faq
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function updateAll($id, Request $request)
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
     * Update faq patch.
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
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
