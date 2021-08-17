<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Validation\Rules\Exists;

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
     *   path="/api/v1/users/{idUser}/favorites",
     *   summary="Return all Favorites of one User",
     *   tags={"Favorite Controller"},
     * security={{ "apiAuth": {} }},
     *   @OA\Parameter(ref="#/components/parameters/get_request_parameter_limit"),
     *  @OA\Parameter(
     *     name="idUser",
     *     in="path",
     *     required=true,
     *     description="ID of the user to get favorite",
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
     *     response=200,
     *     description="List of favorites",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="idFavorite",
     *         default="favorite id",
     *         description="Id of the Favorite",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the favorite creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="Id of User who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the favorite last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of User who modified this one",
     *       ),
     *     )
     *   )
     * )
     */

    public function getAllFavorites($idUser)
    {
        try {
            $favorite = Favorite::all()->where('idUser', $idUser);
            if (empty($favorite))
                return response()->json(['message' => "The user doesn't exist", 'status' => 'fail'], 404);
            return response()->json($favorite, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'favorite not found!' . $e->getMessage()], 409);
        }
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/favorites",
     *   summary="Add a favorite",
     *   tags={"Favorite Controller"},
     *   security={{ "apiAuth": {} }},
     * @OA\Parameter(
     *     name="idUser",
     *     in="query",
     *     required=true,
     *     description="id of User",
     *     @OA\Schema(
     *       type="string", default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="idProperty",
     *     in="query",
     *     required=true,
     *     description="ID of Property",
     *     @OA\Schema(
     *       type="number", default="1"
     *     )
     *     ),
     *  @OA\Parameter(
     *     name="created_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="updated_by",
     *     in="query",
     *     required=true,
     *     description="ID of the logged user",
     *     @OA\Schema(
     *       type="integer", default=1
     *     )
     *   ),
     *     @OA\Property(
     *         property="idFavorite",
     *         default="favorite id",
     *         description="Id of the Favorite",
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default="user id",
     *         description="Id of the user",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the favorite creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="Id of User who created this one",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the favorite last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of User who modified this one",
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
     *         property="idFavorite",
     *         default="Favorite id",
     *         description="Id of the Favorite",
     *       ),
     *       @OA\Property(
     *         property="idUser",
     *         default="user id",
     *         description="Id of the user",
     *       ),
     *       @OA\Property(
     *         property="created_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the favorite creation",
     *       ),
     *       @OA\Property(
     *         property="created_by",
     *         default=1,
     *         description="Id of creator",
     *       ),
     *       @OA\Property(
     *         property="updated_at",
     *         default="2021-02-05T09:00:57.000000Z",
     *         description="Timestamp of the Favorite last update",
     *       ),
     *       @OA\Property(
     *         property="updated_by",
     *         default=1,
     *         description="Id of creator",
     *       ),
     *     )
     *   ),
     * )
     */


    public function toggleFavorite(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'idProperty' => 'required|integer',
            'idUser' => 'required|integer',
        ]);

        try {
            $favorite = new Favorite;
            $favorite->idProperty = $request->input('idProperty');
            $favorite->idUser = $request->input('idUser');
            
            $exist = Favorite::all()->where("idUser", $request->input("idUser"))->where("idProperty", $request->input("idProperty"))->first();
            $save = $exist;

            if ($exist) {
                $exist->delete();
            } else {

                $exist = new Favorite;
                $exist->idProperty = $request->input('idProperty');
                $exist->idUser = $request->input('idUser');

                $exist->save();
            }

            // Return successful response
            return response()->json(['favorite' => $exist ?? $save, 'message' => 'Favorite successfully toggled !!!', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Favorite add Failed!' .  $e->getMessage(), 'status' => 'fail'], 409);
        }
    }
}
