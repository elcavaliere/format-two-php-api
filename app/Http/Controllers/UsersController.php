<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class UsersController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth:api');
    }

    /**
     * @SWG\Get(
     *     path="/users",
     *     tags={"Users"},
     *     operationId="users",
     *     summary="Get users list",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer access_token ( Obtained after logging in )",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Got users list successfully"
     *    ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad request"
     *    ),
     *     @SWG\Response(
     *         response="404",
     *         description="Resource Not Found"
     *    ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized"
     *    )
     * )
     */

    /**
     * Get users list
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json([
           'users' => User::with('profile')->orderBy('created_at','desc')->get()
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/users/{id}/balance",
     *     tags={"Users"},
     *     operationId="user_balance",
     *     summary="Get given user's balance",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer access_token ( Obtained after logging in )",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         description="User id",
     *         in="path",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Got users list successfully"
     *    ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad request"
     *    ),
     *     @SWG\Response(
     *         response="404",
     *         description="Resource Not Found"
     *    ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized"
     *    )
     * )
     */

    /**
     * Get authenticated user's balance
     *
     * @param $id
     * @return JsonResponse
     */
    public function balance($id)
    {
        try {
            if($user = User::find($id))
            {
                return response()->json([
                    'balance' => $user->balance
                ]);
            }else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'user don\'t exist',
                ],404);
            }
        }catch (\Exception $e)
        {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
                ,
            ],$e->getCode());
        }
    }
}
