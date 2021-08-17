<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Favorite;
use App\Models\User;

class FavoriteController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // methods with authorization
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *   path="/api/v1/users/{id}/favorites",
     *   summary="Return all Favorites",
     *   tags={"Favorite Controller"},
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
     *     description="List of favorites",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property"idFavorite",
     *         default="favorite id",
     *         description="Id of the Favorite",
     *       ),
     *       @OA\Property(
     *         property"created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the favorite creation",
     *       ),
     *       @OA\Property(
     *         property"created_by",
     *         default=1,
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property"updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the favorite last update",
     *       ),
     *       @OA\Property(
     *         property"updated_by",
     *         default=1,
     *         description="Id of user who modified this one",
     *       ),
     *     )
     *   )
     * )
     */

    public function getFavorites(Request $id)
    {
        try {
            $favorite = Favorite::all()->where('idFavorite', $id)->first();

            if (empty($favorite))
                return response()->json(['message' => "The favorite $id doesn't exist", 'status' => 'fail'], 404);

            return response()->json($favorite, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'favorite not found!' . $e->getMessage()], 409);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/v1/users/{id}/favorite",
     *   summary="Add a favorite",
     *   tags={"Favorite Controller"},
     *   security={{ "apiAuth": {} }},
     *     @OA\Property(
     *         property"idFavorite",
     *         default="favorite id",
     *         description="Id of the Favorite",
     *       ),
     *       @OA\Property(
     *         property"idUser",
     *         default="user id",
     *         description="Id of the user",
     *       ),
     *       @OA\Property(
     *         property"created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the favorite creation",
     *       ),
     *       @OA\Property(
     *         property"created_by",
     *         default=1,
     *         description="Id of user who created this one",
     *       ),
     *       @OA\Property(
     *         property"updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the favorite last update",
     *       ),
     *       @OA\Property(
     *         property"updated_by",
     *         default=1,
     *         description="Id of user who modified this one",
     *       ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Resource Not Found",
     *   ),
     *   @OA\Response(
     *       response=409,
     *       description="Favorite creation failed!",
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Favorite created",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property"idFavorite",
     *         default="Favorite id",
     *         description="Id of the Favorite",
     *       ),
     *       @OA\Property(
     *         property"idUser",
     *         default="user id",
     *         description="Id of the user",
     *       ),
     *       @OA\Property(
     *         property"created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the favorite creation",
     *       ),
     *       @OA\Property(
     *         property"created_by",
     *         default=1,
     *         description="Id of creator",
     *       ),
     *       @OA\Property(
     *         property"updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the Favorite last update",
     *       ),
     *       @OA\Property(
     *         property"updated_by",
     *         default=1,
     *         description="Id of creator",
     *       ),
     *     )
     *   ),
     * )
     */
    public function addFavorite(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'idFavorite' => 'required|string',
            'idUser' => 'required|string',
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        try {
            $favorite = new Favorite;
            $favorite->idFavorite = $request->input('idFavorite');
            $favorite->idUser = $request->input('idUser');
            //test if the creator exists
            $exist = User::find($request->input('created_by'));
            if (!$exist)
                return response()->json(['favorite' => null, 'message' => 'Unknown creator', 'status' => 'fail'], 404);
            $favorite->created_by = $request->input('created_by');
            //test if the creator exists
            $exist = User::find($request->input('updated_by'));
            if (!$exist)
                return response()->json(['favorite' => null, 'message' => 'Unknown user', 'status' => 'fail'], 404);
            $favorite->updated_by = $request->input('updated_by');

            $favorite->save();

            // Return successful response
            return response()->json(['property' => $favorite, 'message' => 'Favorite successfully created!', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Favorite add Failed!', 'status' => 'fail'], 409);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/users/{id}/favorite",
     *   summary="Delete a Favorite",
     *   tags={"Favorite Controller"},
     *   security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="idFavorite",
     *     in="path",
     *     required=true,
     *     description="ID of the Favorite to delete",
     *     @OA\Schema(
     *       type="integer", default=1
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
     *       response=409,
     *       description="Favorite deletion failed!",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Favorite deleted",
     *     @OA\JsonContent(
     *        @OA\Property(
     *         property"idFavorite",
     *         default="favorite id",
     *         description="Id of the Favorite",
     *       ),
     *       @OA\Property(
     *         property"idUser",
     *         default="user id",
     *         description="Id of the user",
     *       ),
     *      )
     *   ),
     * )
     */
    public function deleteFavorite($id)
    {
        try {
            $favorite = Favorite::find($id);

            if (!$favorite)
                return response()->json(['favorite' => $favorite, 'message' => 'Favorite successfully deleted!', 'status' => 'success'], 200);

            $favorite->delete();

            return response()->json(['favorite' => $favorite, 'message' => 'Favorite successfully deleted!', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['message' => 'Favorite deletion failed!', 'status' => 'fail'], 409);
        }
    }
}
