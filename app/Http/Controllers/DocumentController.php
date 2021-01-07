<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
 

class DocumentController extends Controller
{
    /**
     * Get all documents
     *
     * @param  Request  $request
     * @return Response
     */
    public function allDocument(Request $request)
    {
        return response()->json(['document' =>  Document::all()], 200);
    }

    /**
     * Get one document
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneDocument($id)
    {
        try {
            $document = Document::all()->where('idDocument', $id)->first();

            return response()->json(['document' => $document], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Document not found!' . $e->getMessage()], 404);
        }
    }
    /**
     * Store a new document.
     *
     * @param  Request  $request
     * @return Response
     */
    
    public function registerDocument(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameDocument' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {

            $document = new Document;
            $document->nameDocument = $request->input('nameDocument');
            $document->created_by = $request->input('created_by');
            $document->updated_by = $request->input('updated_by');

            $document->save();

            //return successful response
            return response()->json(['document' => $document, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document Registration Failed!' . $e->getMessage()], 409);
        }
    }
}
