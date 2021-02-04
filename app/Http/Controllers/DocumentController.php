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
            $document = Document::all()
                ->where('idDocument', $id)
                ->first();
            $document['data'] = $this->getAllData($id)->original;
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
                if (!$this->_addData($document->idDocument, $request))
                    return response()->json(['message' => 'Document data not added!', 'status' => 'fail'], 500);
            }

            // Return successful response
            return response()->json(['document' => $document, 'message' => 'CREATED', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Document Registration Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * Update document
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function updateDocument($id, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'nameDocument' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
            'data' => 'string',
        ]);

        try {
            // Update
            $document = Document::findOrFail($id);
            if ($request->input('nameDocument') !== null)
                $document->nameDocument = $request->input('nameDocument');
            if ($request->input('created_by') !== null)
                $document->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $document->updated_by = $request->input('updated_by');

            $document->update();

            // Updatedata
            if ($request->input('data') !== null) {
                $data = (array)json_decode($request->input('data'), true);

                foreach ($data as $key => $value) {
                    if (!$this->updateData($document->idDocument, $key, $value))
                        return response()->json(['message' => 'Document Update Failed!', 'status' => 'fail'], 500);
                }
            }
            // Return successful response
            return response()->json(['document' => $document, 'data' => $this->getAllData($document->idDocument)->original, 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
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

            // Update data
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

    // Route
    public function addData($id, Request $request)
    {
        try {
            if (!$this->_addData($id, $request))
                return response()->json(['message' => 'Not all data has been added', 'status' => 'fail'], 409);

            // Return successful response
            return response()->json(['data' => $this->getAllData($id)->original, 'message' => 'Data created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'User data not added!', 'status' => 'fail'], 409);
        }
    }
    // fonction utilisée par la route et lors de la creation de user pour ajouter toutes les data
    public function _addData($idDocument, $request)
    {
        $data = (array)json_decode($request->input('data'), true);

        try {
            foreach ($data as $key => $value) {

                $documentData = new DocumentData;
                $documentData->keydocumentData = $key;
                $documentData->valuedocumentData = $value;
                $documentData->created_by = $request->input('created_by');
                $documentData->updated_by = $request->input('updated_by');
                $documentData->idDocument = $idDocument;

                $documentData->save();
            }

            // Return successful response
            return true;
        } catch (\Exception $e) {
            // Return error message
            return false;
        }
    }

    public function getAllData($idDocument)
    {
        $data = array();
        foreach (DocumentData::all()->where('idDocument', $idDocument) as $value) {
            array_push($data, $value);
        }
        return response()->json($data, 200);
    }

    public function getData($idDocument, $key)
    {
        return response()->json(
            DocumentData::all()
            ->where('idDocument', $idDocument)
            ->where('keyDocumentData', $key),
            200
            );
    }

    public function updateData($idDocument, $key, $value)
    {
        try {
            $documentData = DocumentData::all()
            ->where('idDocument', $idDocument)
            ->where('keyDocumentData', $key)
            ->first();

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
            $documentData = DocumentData::all()
            ->where('idDocument', $idDocument)
            ->where('keyDocumentData', $key)
            ->first();

            if ($documentData == null)
                return false;

            $documentData->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
