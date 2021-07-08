<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentData;
use App\Models\User;

class DocumentDataController extends Controller
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
     *   path="/api/v1/documents/{id}/data",
     *   summary="Return all data of specific document",
     *   tags={"DocumentData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the document",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found"
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data could not be retrieved"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idDocumentData",
     *         default=1,
     *         description="Id of the document data",
     *       ),
     *       @OA\Property(
     *         property="keyDocumentData",
     *         default="Any key",
     *         description="Key of the document data",
     *       ),
     *       @OA\Property(
     *         property="valueDocumentData",
     *         default="Any value",
     *         description="Value of the document data",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="ID of user who did the last update",
     *       ),
     *       @OA\Property(
     *         property="idDocument",
     *         default=1,
     *         description="ID of the document that this data is related to",
     *       ),
     *     )
     *   )
     * )
     */
    public function getAllData($id)
    {
        try {
            //if document doesn't exists
            if (!$this->existDocument($id))
                return response()->json(['data' => null, 'message' => "Document doesn't exists", 'status' => 'fail'], 404);

            $data = array_values(DocumentData::all()->where('idDocument', $id)->toArray());

            return response()->json(['total' => count($data), 'data' => $data, 'message' => 'Document data successfully retrieved', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/v1/documents/{id}/data/{key}",
     *   summary="Return specific data of the specified document",
     *   tags={"DocumentData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the concerned document",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="key of the document to get",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No data for this key"
     *   ),
     *   @OA\Response(
     *     response=409,
     *     description="Server error"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Requested data",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idDocumentData",
     *         default=1,
     *         description="key of the document",
     *       ),
     *       @OA\Property(
     *         property="keyDocumentData",
     *         default="key",
     *         description="key of the document",
     *       ),
     *       @OA\Property(
     *         property="valueDocumentData",
     *         default="Any value",
     *         description="Value of the document",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="Creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-03-04T11:45:35.000000Z",
     *         description="Date of update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Who did the last update",
     *       ),
     *       @OA\Property(
     *         property="idDocument",
     *         default=1,
     *         description="Document associated with the data",
     *       ),
     *     )
     *   ),
     * )
     */
    public function getDocumentData($id, $key)
    {
        try {
            //if document doesn't exists
            if (!$this->existDocument($id))
                return response()->json(['data' => null, 'message' => "Document doesn't exists", 'status' => 'fail'], 404);

            $documentData = DocumentData::all()
                ->where('idDocument', $id)
                ->where('keyDocumentData', $key)
                ->first();

            //key doesn't exists
            if (!$documentData)
                return response()->json(['data' => null, 'message' => "No data for this key", 'status' => 'fail'], 404);

            return response()->json(['data' => $documentData, 'message' => 'Data successfully retrieved!', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data recovery failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/documents/{id}/data",
     *   summary="Add a data to a specific document",
     *   tags={"DocumentData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the document",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyDocumentData",
     *     in="query",
     *     required=true,
     *     description="Key of the document data",
     *     @OA\Schema(
     *       type="string", default="Any key"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueDocumentData",
     *     in="query",
     *     required=true,
     *     description="Value of the document data",
     *     @OA\Schema(
     *       type="string", default="Any value"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the creator",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the last user who changed this line",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Unknown Document"
     *   ),
     *   @OA\Response(
     *     response=409,
     *     description="Data addition failed!",
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Data added",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idDocumentData",
     *         default=1,
     *         description="Id of the document",
     *       ),
     *       @OA\Property(
     *         property="keyDocumentData",
     *         default="Some key",
     *         description="Key to add",
     *       ),
     *       @OA\Property(
     *         property="valueDocumentData",
     *         default="Any value",
     *         description="Value of the key to add",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="ID of user who has updated",
     *       ),
     *       @OA\Property(
     *         property="idDocument",
     *         default=1,
     *         description="ID of the related document",
     *       ),
     *     )
     *   ),
     * )
     */
    public function addDocumentData($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyDocumentData' => 'required|string',
            'valueDocumentData' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            //if document or users doesn't exist
            $created_by = User::all()->where('idUser', $request->input('created_by'))->first();
            $updated_by = User::all()->where('idUser', $request->input('updated_by'))->first();
            if (!$this->existDocument($id))
                return response()->json(['data' => null, 'message' => "Unknown Document", 'status' => 'fail'], 404);
            if (!$created_by)
                return response()->json(['data' => null, 'message' => "Creator unknown", 'status' => 'fail'], 404);
            if (!$updated_by)
                return response()->json(['data' => null, 'message' => "User unknown", 'status' => 'fail'], 404);

            //if document data already exists
            $exist = DocumentData::all()
                ->where('keyDocumentData', $request->input('keyDocumentData'))
                ->where('idDocument', $id)
                ->first();
            if ($exist)
                return response()->json(['data' => null, 'message' => "Data already exists", 'status' => 'fail'], 404);

            //creation of the new data
            $documentData = new DocumentData;
            $documentData->keyDocumentData = $request->input('keyDocumentData');
            $documentData->valueDocumentData = $request->input('valueDocumentData');
            $documentData->created_by = (int)$request->input('created_by');
            $documentData->updated_by = (int)$request->input('updated_by');
            $documentData->idDocument = (int)$id;
            $documentData->save();

            // Return successful response
            return response()->json(['documentData' => $documentData, 'message' => 'Document data successfully created', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document Data addition failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/documents/{id}/data/{key}",
     *   summary="Update a document data",
     *   tags={"DocumentData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Key of the document related to the data to update",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the document data to update",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="keyDocumentData",
     *     in="query",
     *     required=false,
     *     description="New keyDocumentData",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="valueDocumentData",
     *     in="query",
     *     required=false,
     *     description="New valueDocumentData",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=false,
     *     description="New creator",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=false,
     *     description="New modifier",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="idDocument",
     *     in="query",
     *     required=false,
     *     description="New idDocument",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data update failed",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Document data updated",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idDocumentData",
     *         default=1,
     *         description="Id of the document data",
     *       ),
     *       @OA\Property(
     *         property="keyDocumentData",
     *         default="thumbnail",
     *         description="Key of the document data",
     *       ),
     *       @OA\Property(
     *         property="valueDocumentData",
     *         default="any value",
     *         description="Value of the document data",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="ID of user that modifier this data",
     *       ),
     *       @OA\Property(
     *         property="idDocument",
     *         default=1,
     *         description="ID of document this data is related to",
     *       ),
     *     )
     *   ),
     * )
     */
    public function updateDocumentData($id, $key, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'keyDocumentData' => 'string',
            'valueDocumentData' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'idDocument' => 'integer',
        ]);

        try {
            //if users doesn't exist
            if ($request->input('created_by')) {
                $created_by = User::all()->where('idUser', $request->input('created_by'))->first();
                if (empty($created_by))
                    return response()->json(['data' => null, 'message' => "Creator unknown", 'status' => 'fail'], 404);
            }
            if ($request->input('updated_by')) {
                $updated_by = User::all()->where('idUser', $request->input('updated_by'))->first();
                if (empty($updated_by))
                    return response()->json(['data' => null, 'message' => "User unknown", 'status' => 'fail'], 404);
            }

            //if document doesn't exists
            if (!$this->existDocument($id))
                return response()->json(['data' => null, 'message' => "Unknown document", 'status' => 'fail'], 404);

            //test if the new key already exists
            $newKeyExist = DocumentData::all()
                ->where('idDocument', $id)
                ->where('keyDocumentData', $request->input('keyDocumentData'))
                ->first();
            if ($newKeyExist)
                return response()->json(['message' => 'Data with this key already exists', 'status' => 'fail'], 404);

            // update
            $documentData = DocumentData::all()
                ->where('idDocument', $id)
                ->where('keyDocumentData', $key)
                ->first();
            if (!$documentData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            if ($request->input('keyDocumentData') !== null)
                $documentData->keyDocumentData = $request->input('keyDocumentData');
            if ($request->input('valueDocumentData') !== null)
                $documentData->valueDocumentData = $request->input('valueDocumentData');
            if ($request->input('created_by') !== null)
                $documentData->created_by = (int)$request->input('created_by');
            if ($request->input('updated_by') !== null)
                $documentData->updated_by = (int)$request->input('updated_by');
            if ($request->input('idDocument') !== null)
                $documentData->idDocument = (int)$request->input('idDocument');

            $documentData->update();

            // Return successful response
            return response()->json(['data' => $documentData, 'message' => 'Data successfully updated', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Document data Update Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/documents/{id}/data/{key}",
     *   summary="Delete a document data",
     *   tags={"DocumentData Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the document data to delete",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="key",
     *     in="path",
     *     required=true,
     *     description="Key of the document data to delete",
     *     @OA\Schema(
     *       type="string", default="thumbnail"
     *     )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="No data for this key"
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Data deletion failed!",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Document data deleted",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="keyDocumentData",
     *         default="Any key",
     *         description="Key of the document data",
     *       ),
     *       @OA\Property(
     *         property="valueDocumentData",
     *         default="any value",
     *         description="Value of the document data",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="ID of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="ID of user who deleted this data",
     *       ),
     *       @OA\Property(
     *         property="idDocument",
     *         default=1,
     *         description="ID of document this data was related to",
     *       )
     *      )
     *   ),
     * )
     */
    public function deleteDocumentData($id, $key)
    {
        try {
            $documentData = DocumentData::all()
                ->where('idDocument', $id)
                ->where('keyDocumentData', $key)
                ->first();

            if (!$documentData)
                return response()->json(['message' => 'No data for this key', 'status' => 'fail'], 404);

            $documentData->delete();

            return response()->json(['data' => $documentData, 'message' => 'Data successfully deleted', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Data deletion failed!', 'status' => 'fail'], 409);
        }
    }

    private function existDocument($id)
    {
        $document = Document::all()
            ->where('idDocument', $id)
            ->first();
        return (bool) $document;
    }
}
