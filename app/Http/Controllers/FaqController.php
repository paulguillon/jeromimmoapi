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
}
