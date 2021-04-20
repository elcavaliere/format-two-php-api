<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use Swagger\Annotations as SWG;

class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @SWG\Get(
     *     path="/transactions",
     *     tags={"Transactions"},
     *     operationId="transactions",
     *     summary="Get transactions list",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Got transactions list successfully"
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

    public function index()
    {
        return response()->json([
           'transactions' => Transaction::with('user')
                            ->orderBy('created_at','desc')
                            ->get()
        ]);
    }

    /**
     * @SWG\Post(
     *     path="/transactions/create",
     *     tags={"Transactions"},
     *     operationId="transaction_create",
     *     summary="Register new transaction",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="value",
     *         description="Transaction value",
     *         in="formData",
     *         required=true,
     *         type="number",
     *         format="varchar"
     *     ),
     *    @SWG\Parameter(
     *         name="type",
     *         description="Transaction type (1 0r 2)",
     *         in="formData",
     *         required=true,
     *         type="integer",
     *         format="varchar"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Transaction created successfully"
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
     *    ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error"
     *    )
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function store(Request $request)
    {
        $this->validate($request,[
           'value' => 'required',
           'type' => 'required'
        ]);

        try {

            $description = $request->type == 1 ?
                'Пополнение счета' : 'Списание от счета';

            $transaction = Transaction::create([
                'user_id' => auth()->user()->id,
                'value' => $request->value,
                'type' => $request->type,
                'description' => $description
            ]);

            $newBalance = $transaction->type == 1 ? ($transaction->user->profile->balance + $transaction->value) : ($transaction->user->profile->balance - $transaction->value);
            $transaction->user->profile->balance = $newBalance;
            $transaction->user->profile->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction created successfully',
                'transaction' => $transaction,
            ]);

        }catch (\Exception $e)
        {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @SWG\Get(
     *     path="/transactions/{id}",
     *     tags={"Transactions"},
     *     operationId="transaction_show",
     *     summary="Get a single transaction by id",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         description="Transaction id",
     *         in="path",
     *         required=true,
     *         type="number"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Got transaction successfully"
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
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {

            if($transaction = Transaction::find($id))
            {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction retrieved successfully',
                    'transaction' => $transaction
                ]);

            }else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction don\'t exist',
                ],404);
            }

        }catch (\Exception $e)
        {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @SWG\Get(
     *     path="/transactions/{id}/cancel",
     *     tags={"Transactions"},
     *     operationId="transaction_cancel",
     *     summary="Cancel a single transaction by id",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         description="Transaction id",
     *         in="path",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Transaction canceled successfully"
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
     * @param $id
     * @return JsonResponse
     */
    public function cancel($id)
    {
        try {

            $transaction = null;

            if($transaction = Transaction::find($id))
            {
                if($transaction->type != 3)
                {
                    $newBalance = $transaction->type == 1 ? ($transaction->user->profile->balance - $transaction->value) : ($transaction->user->profile->balance + $transaction->value);
                    $transaction->user->profile->balance = $newBalance;
                    $transaction->user->profile->save();

                    $transaction->type = 3;
                    $transaction->description = 'Отмененная транзакция';
                    $transaction->save();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaction canceled successfully',
                        'transaction' => $transaction
                    ]);

                }else{
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Transaction already canceled',
                    ]);
                }

            }else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction don\'t exist',
                ],404);
            }

        }catch (\Exception $e)
        {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @SWG\Get(
     *     path="/users/{id}/transactions",
     *     tags={"Transactions"},
     *     operationId="user_transactions",
     *     summary="Get all given user transactions",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer",
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
     *         description="Got user's transactions list successfully"
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
     * Get all given user transactions
     * @param $id
     * @return JsonResponse
     */
    public function userTransactions($id)
    {
        try {
            if($user = User::find($id))
            {
                return response()->json([
                    'transactions' => $user->transactions()
                        ->orderBy('created_at','desc')
                        ->get()
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
                'message' => $e->getMessage(),
            ]);
        }
    }
}
