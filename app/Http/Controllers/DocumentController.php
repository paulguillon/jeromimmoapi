<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentData;


class DocumentController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods authorization
        $this->middleware('auth:api');
    }

    /**
     * Get all documents
     *
     * @param  Request  $request
     * @return Response
     */
    public function allDocument(Request $request)
    {
        return response()->json(['document' =>  Document::all(), 'documentData' => DocumentData::all()], 200);
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
            'keyDocumentData' => 'string',
            'valueDocumentData' => 'string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {

            $document = new Document;
            $document->nameDocument = $request->input('nameDocument');
            $document->created_by = $request->input('created_by');
            $document->updated_by = $request->input('updated_by');
            if(!$document->save())
            return response()->json(['message' => 'Document Registration Failed !'], 409);

            $documentData = new DocumentData;
            $documentData->keyDocumentData = $request->input('keyDocumentData');
            $documentData->valueDocumentData = $request->input('valueDocumentData');
            $documentData->idDocument = $document->idDocument;
            $documentData->created_by = $request->input('created_by');
            $documentData->updated_by = $request->input('updated_by');
            $documentData->save();
            //return successful response
            return response()->json(['document' => $document, 'documentData' => $documentData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Put document
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function put($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameDocument' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $document = Document::findOrFail($id);
            $document->nameDocument = $request->input('nameDocument');
            $document->created_by = $request->input('created_by');
            $document->updated_by = $request->input('updated_by');

            $document->update();

            //return successful response
            return response()->json(['document' => $document, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Patch user
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function patch($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameDocument' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $document = Document::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);

            if ($request->input('nameDocument') !== null)
                $document->nameDocument = $request->input('nameDocument');
            if ($request->input('created_by') !== null)
                $document->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $document->updated_by = $request->input('updated_by');

            $document->update();

            //return successful response
            return response()->json(['document' => $document, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function delete($id)
    {
        try {
            $document = Document::findOrFail($id);
            $document->delete();

            return response()->json(['document' => $document, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
