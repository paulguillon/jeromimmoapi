<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\User;

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
     *   path="/api/v1/documents",
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
     *       @OA\Items(
     *         @OA\Property(
     *           property="idDocument",
     *           default="Document id",
     *           description="Id of the document",
     *         ),
     *         @OA\Property(
     *           property="nameDocument",
     *           default="Document name",
     *           description="Name of the document",
     *         ),
     *         @OA\Property(
     *           property="created_at",
     *           default="2021-02-05T09:00:57.000000Z",
     *           description="Timestamp of the document creation",
     *         ),
     *         @OA\Property(
     *           property="created_by",
     *           default="1",
     *           description="Id of user who created this one",
     *         ),
     *         @OA\Property(
     *           property="updated_at",
     *           default="2021-02-05T09:00:57.000000Z",
     *           description="Timestamp of the document last update",
     *         ),
     *         @OA\Property(
     *           property="updated_by",
     *           default="1",
     *           description="Id of user who modified this one",
     *         ),
     *       ),
     *       )
     *     )
     *   )
     * )
     */
    public function getDocuments(Request $request)
    {
        $documents = Document::all();
        return response()->json($documents, 200);
    }
    /**
     * @OA\Get(
     *   path="/api/v1/documents/{id}",
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

            return response()->json($document, 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Document not found!' . $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/documents",
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
     *   @OA\Response(
     *       response=409,
     *       description="Not created",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
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
        ]);

        try {
            $document = new Document;
            $document->nameDocument = $request->input('nameDocument');
            
            //test if the creator exists
            $exist = User::find($request->input('created_by'));
            if (!$exist)
                return response()->json(['document' => null, 'message' => 'Unknown creator', 'status' => 'fail'], 404);
            $document->created_by = $request->input('created_by');

            //test if the user exists
            $exist = User::find($request->input('updated_by'));
            if (!$exist)
                return response()->json(['document' => null, 'message' => 'Unknown user', 'status' => 'fail'], 404);
            $document->updated_by = $request->input('updated_by');

            $document->save();

            // Return successful response
            return response()->json(['document' => $document, 'message' => 'Document successfully created!', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Document Registration Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/documents/{id}",
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
     *   @OA\Response(
     *       response=409,
     *       description="Not updated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
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
     *     )
     *   ),
     * )
     */
    public function updateDocument($id, Request $request)
    {
        // Validate incoming request
        $this->validate($request, [
            'nameDocument' => 'string',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ]);

        try {
            // Update
            $document = Document::findOrFail($id);
            if ($request->input('nameDocument') !== null)
                $document->nameDocument = $request->input('nameDocument');
            if ($request->input('created_by') !== null) {
                //test if the creator exists
                $exist = User::find($request->input('created_by'));
                if (!$exist)
                    return response()->json(['document' => null, 'message' => 'Unknown creator', 'status' => 'fail'], 404);
                //update if ok
                $document->created_by = $request->input('created_by');
            }
            if ($request->input('updated_by') !== null) {
                //test if the creator exists
                $exist = User::find($request->input('updated_by'));
                if (!$exist)
                    return response()->json(['document' => null, 'message' => 'Unknown user', 'status' => 'fail'], 404);
                //update if ok
                $document->updated_by = $request->input('updated_by');
            }

            $document->update();

            // Return successful response
            return response()->json(['document' => $document, 'message' => 'ALL UPDATED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Document Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/documents/{id}",
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
     *      )
     *   ),
     * )
     */
    public function deleteDocument($id)
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
