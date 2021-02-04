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
    public function getDocuments(Request $request)
    {
        $documents = Document::all();

        for ($i = 0; $i < count($documents); $i++) {
            $document = $documents[$i];

            $document['data'] = $this->getAllData($document->idDocument)->original;
        }

        return response()->json(['documents' =>  $documents], 200);
    }

    /**
     * Get one document
     *
     * @param  Request  $request
     * @return Response
     */
    public function getDocument($id)
    {
        try {
            $document = Document::all()->where('idDocument', $id)->first();
            $document['data'] = $this->getAllData($document->idDocument)->original;
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

    public function addDocument(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameDocument' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
            'data' => 'string',
        ]);

        try {
            $document = new Document;
            $document->nameDocument = $request->input('nameDocument');
            $document->created_by = $request->input('created_by');
            $document->updated_by = $request->input('updated_by');

            $document->save();

            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->addData($document->idDocument, $key, $value, $request))
                        return response()->json(['message' => 'Document data not added!', 'status' => 'fail'], 500);
                }
            }
            //return successful response
            return response()->json(['document' => $document, 'message' => 'CREATED', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document Registration Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * Patch document
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function updateDocument($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'nameDocument' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
            'data' => 'string',
        ]);

        try {
            // On modifie les infos principales du document
            $document = Document::findOrFail($id);
            if ($request->input('nameDocument') !== null)
                $document->nameDocument = $request->input('nameDocument');
            if ($request->input('created_by') !== null)
                $document->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $document->updated_by = $request->input('updated_by');

            $document->update();

            //maj des data
            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->updateData($document->idDocument, $key, $value))
                        return response()->json(['message' => 'Document Update Failed!', 'status' => 'fail'], 500);
                }
            }
            //return successful response
            return response()->json(['document' => $document, 'data' => $this->getAllData($document->idDocument)->original, 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * Delete document function
     *
     * @param int $id
     * @return Response
     */
    public function deleteDocument($id)
    {
        try {
            $document = Document::findOrFail($id);
            $documentData = DocumentData::all()->where('idDocument', $id);

            //maj des data
            if ($documentData !== null) {
                foreach ($documentData as $key => $value) {
                    if (!$this->deleteData($document->idDocument, $key))
                        return response()->json(['message' => 'Document Deletion Failed!', 'status' => 'fail'], 500);
                }
            }

            $document->delete();

            return response()->json(['document' => $document, 'data' => $documentData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function addData($idDocument, $key, $value, $request)
    {
        try {
            $documentData = new DocumentData;
            $documentData->keydocumentData = $key;
            $documentData->valuedocumentData = $value;
            $documentData->created_by = $request->input('created_by');
            $documentData->updated_by = $request->input('updated_by');
            $documentData->idDocument = $idDocument;

            $documentData->save();

            //return successful response
            return response()->json(['document' => $documentData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document data not added!' . $e->getMessage()], 409);
        }
    }

    public function getAllData($idDocument)
    {
        return response()->json(DocumentData::all()->where('idDocument', $idDocument), 200);
    }

    public function getData($idDocument, $key)
    {
        return response()->json(DocumentData::all()->where('idDocument', $idDocument)->where('keyDocumentData', $key), 200);
    }

    public function updateData($idDocument, $key, $value)
    {
        try {
            $documentData = DocumentData::all()->where('idDocument', $idDocument)->where('keyDocumentData', $key)->first();

            if ($documentData == null)
                return false;

            $documentData->valueDocumentData = $value;
            $documentData->update();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteData($idDocument, $key)
    {
        try {
            $documentData = DocumentData::all()->where('idDocument', $idDocument)->where('keyDocumentData', $key)->first();

            if ($documentData == null)
                return false;

            $documentData->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
