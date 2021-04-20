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
     *         description="Bearer",
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
     *     path="/balance",
     *     tags={"Users"},
     *     operationId="balance",
     *     summary="Get authenticated user's balance",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer",
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
     * Get authenticated user's balance
     * @return JsonResponse
     */
    public function balance()
    {
        return response()->json([
            'balance' => auth()->user()->balance
        ]);
    }
}
