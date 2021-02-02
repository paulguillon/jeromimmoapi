<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;

class DocumentDataController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods without authorization
        $this->middleware('auth:api', ['except' => []]);
    }

    /**
     * Get all Document data
     *
     * @param  Request  $request
     * @return Response
     */
    public function allDocumentData(Request $request)
    {
        return response()->json(['documentData' =>  DocumentData::all()], 200);
    }

    /**
     * Get one Document data
     *
     * @param  Request  $request
     * @return Response
     */
    public function oneDocumentData($id)
    {
        try {
            $documentData = DocumentData::all()->where('idDocumentData', $id)->first();

            return response()->json(['document' => $documentData], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Document data not found!' . $e->getMessage()], 404);
        }
    }

    /**
     * Store a new document data.
     *
     * @param  Request  $request
     * @return Response
     */
    public function registerDocumentData(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyDocumentData' => 'required|string',
            'valueDocumentData' => 'required|string',
            'idDocument' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {

            $documentData = new DocumentData;
            $documentData->keyDocumentData = $request->input('keyDocumentData');
            $documentData->valueDocumentData = $request->input('valueDocumentData');
            $documentData->idDocument = $request->input('idDocument');
            $documentData->created_by = $request->input('created_by');
            $documentData->updated_by = $request->input('updated_by');

            $documentData->save();

            //return successful response
            return response()->json(['documentData' => $documentData, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document Data Registration Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update document data
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function put($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyDocumentData' => 'required|string',
            'valueDocumentData' => 'required|string',
            'idDocument' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $documentData = DocumentData::findOrFail($id);
            $documentData->keyDocumentData = $request->input('keyDocumentData');
            $documentData->valueDocumentData = $request->input('valueDocumentData');
            $documentData->idDocument = $request->input('idDocument');
            $documentData->created_by = $request->input('created_by');
            $documentData->updated_by = $request->input('updated_by');

            $documentData->update();

            //return successful response
            return response()->json(['documentData' => $documentData, 'message' => 'ALL UPDATED'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document Data Update Failed!' . $e->getMessage()], 409);
        }
    }

    /**
     * Update document patch.
     *
     * @param  string   $id
     * @param  Request  $request
     * @return Response
     */
    public function patch($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'keyDocumentData' => 'required|string',
            'valueDocumentData' => 'required|string',
            'idDocument' => 'required|integer',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer'
        ]);

        try {
            $documentData = DocumentData::findOrFail($id);

            if (in_array(null or '', $request->all()))
                return response()->json(['message' => 'Null or empty value', 'status' => 'fail'], 500);
            if ($request->input('keyDocumentData') !== null)
                $documentData->keyDocumentData = $request->input('keyDocumentData');
            if ($request->input('valueDocumentData') !== null)
                $documentData->valueDocumentData = $request->input('valueDocumentData');
            if ($request->input('idDocument') !== null)
                $documentData->idDocument = $request->input('idDocument');
            if ($request->input('created_by') !== null)
                $documentData->created_by = $request->input('created_by');
            if ($request->input('updated_by') !== null)
                $documentData->updated_by = $request->input('updated_by');

            $documentData->update();

            //return successful response
            return response()->json(['documentData' => $documentData, 'message' => 'PATCHED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document data Update Failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }

    public function delete($id)
    {
        try {
            $documentData = DocumentData::findOrFail($id);
            $documentData->delete();

            return response()->json(['documentData' => $documentData, 'message' => 'DELETED', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Document data deletion failed!' . $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
