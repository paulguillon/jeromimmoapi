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
     * @OA\Get(
     *   path="/api/v1/document",
     *   summary="Return all documents",
     *   tags={"Document Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(ref="#/components/parameters/get_request_parameter_limit"),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of documents",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idDocument",
     *         default="Document id",
     *         description="Id of the document",
     *       ),
     *       @OA\Property(
     *         property="nameDocument",
     *         default="Document name",
     *         description="Name of the document",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the document creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the document last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default={"keyData":"valueData"},
     *         description="Document data",
     *       ),
     *     )
     *   )
     * )
     */
    public function getDocuments(Request $request)
    {
        $documents = Document::all();

        for ($i = 0; $i < count($documents); $i++) {
            $document = $documents[$i];

            $document['data'] = $this->getAllData($document->idDocument);
        }

        return response()->json(['documents' =>  $documents], 200);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/document/{id}",
     *   summary="Return a document",
     *   tags={"Document Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the document to get",
     *     @OA\Schema(
     *       type="number", default=1
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Document not found",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="The document ? doesn't exist",
     *          description="Message",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="fail",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of documents",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idDocument",
     *         default="Document id",
     *         description="Id of the document",
     *       ),
     *       @OA\Property(
     *         property="nameDocument",
     *         default="Document name",
     *         description="Name of the document",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the document creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the document last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default={"keyData":"valueData"},
     *         description="Document data",
     *       ),
     *     )
     *   )
     * )
     */
    public function getDocument($id)
    {
        try {
            $document = Document::all()
                ->where('idDocument', $id)
                ->first();
            $document['data'] = $this->getAllData($id);
            return response()->json(['document' => $document], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Document not found!' . $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/document",
     *   summary="Add a document",
     *   tags={"Document Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="nameDocument",
     *     in="query",
     *     required=true,
     *     description="Name of the document to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     required=true,
     *     description="Data of the document to add",
     *     @OA\Schema(
     *       type="string", default={"keyData":"valueData"}
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Not created",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Document data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Document data not added",
     *          description="Message",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="fail",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Document created",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idDocument",
     *         default="Document id",
     *         description="Id of the document",
     *       ),
     *       @OA\Property(
     *         property="nameDocument",
     *         default="Document name",
     *         description="Name of the document",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the document creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the document last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default={"keyData":"valueData"},
     *         description="Document data",
     *       ),
     *     )
     *   ),
     * )
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
     * @OA\Patch(
     *   path="/api/v1/document/{id}",
     *   summary="Update a document",
     *   tags={"Document Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the document to update",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="nameDocument",
     *     in="query",
     *     required=true,
     *     description="Type of the document to add",
     *     @OA\Schema(
     *       type="string", default="first"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     required=true,
     *     description="Data of the document to add",
     *     @OA\Schema(
     *       type="string", default={"keyData":"valueData"},
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Not updated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Document data not updated",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Document data not updated",
     *          description="Message",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="fail",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Document updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idDocument",
     *         default="Document id",
     *         description="Id of the document",
     *       ),
     *       @OA\Property(
     *         property="nameDocument",
     *         default="Document name",
     *         description="Type of the document",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the document creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the document last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default={"keyData":"valueData"},
     *         description="Document data",
     *       ),
     *     )
     *   ),
     * )
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
            return response()->json(['document' => $document, 'data' => $this->getAllData($document->idDocument), 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Document Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/document/{id}",
     *   summary="Delete a document",
     *   tags={"Document Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the document to delete",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Not deleted",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Document data not deleted"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Document deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="nameDocument",
     *         default="Document name",
     *         description="Type of the document",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default="1",
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default="1",
     *         description="Id of user who modified this one",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         default={"keyData":"valueData"},
     *         description="Document data",
     *       )
     *      )
     *   ),
     * )
     */
    public function deleteDocument($id)
    {
        try {
            $document = Document::findOrFail($id);
            $documentData = $this->getAllData($id);

            // Update data
            if ($documentData !== null) {
                if (!$this->deleteData($id))
                    return response()->json(['message' => 'Document Deletion Failed!', 'status' => 'fail'], 500);
            }

            $document->delete();

            return response()->json(['document' => $document, 'data' => $documentData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/document/data/{id}",
     *   summary="Add document data",
     *   tags={"Document Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the document",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     required=true,
     *     description="Data of the document to add",
     *     @OA\Schema(
     *       type="string", default={"keyData":"valueData"},
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data not created",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Document data not added",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="message",
     *          default="Document data not added",
     *          description="Message",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="fail",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Document data created",
     *       @OA\JsonContent(
     *        @OA\Property(
     *          property="data",
     *          default={"keyData":"valueData"},
     *          description="data",
     *        ),
     *        @OA\Property(
     *          property="status",
     *          default="success",
     *          description="Status",
     *        ),
     *       ),
     *   ),
     * )
     */
    public function addData($id, Request $request)
    {
        try {
            if (!$this->_addData($id, $request))
                return response()->json(['message' => 'Not all data has been added', 'status' => 'fail'], 409);

            // Return successful response
            return response()->json(['data' => $this->getAllData($id), 'message' => 'Data created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Document data not added!', 'status' => 'fail'], 409);
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
        return response()->json($data, 200)->original;
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

    public function deleteData($idDocument)
    {
        try {
            $documentData = DocumentData::all()->where('idDocument', $idDocument);

            foreach ($documentData as $data) {
                $data->delete();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
