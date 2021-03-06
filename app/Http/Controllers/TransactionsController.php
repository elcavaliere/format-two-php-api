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
     *         description="Bearer access_token ( Obtained after logging in )",
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

        //return all created transactions

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
     *         description="Bearer access_token ( Obtained after logging in )",
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

        //Validate request data

        $this->validate($request,[
           'value' => 'required',
           'type' => 'required'
        ]);

        try {

            $description = $request->type == 1 ?
                '???????????????????? ??????????' : '???????????????? ???? ??????????';

            // Create new a new transaction for the authenticated user

            $transaction = Transaction::create([
                'user_id' => auth()->user()->id,
                'value' => $request->value,
                'type' => $request->type,
                'description' => $description
            ]);

            //Calculate the user's balance based on the type of transaction that has been refunded

            $newBalance = $transaction->type == 1 ? ($transaction->user->profile->balance + $transaction->value) : ($transaction->user->profile->balance - $transaction->value);
            $transaction->user->profile->balance = $newBalance;
            $transaction->user->profile->save();

            //return the created transaction with success message

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
            ],$e->getCode());
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
     *         description="Bearer access_token ( Obtained after logging in )",
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

            //Check if the given id corresponds to that of at least one of the created transactions

            if($transaction = Transaction::find($id))
            {

                //return the transaction with success message

                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction retrieved successfully',
                    'transaction' => $transaction
                ]);

            }else{

                //Returns a 404 error since the given id does not match that of any transaction

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
            ],$e->getCode());
        }
    }

    /**
     * @SWG\Get(
     *     path="/transactions/{id}/refund",
     *     tags={"Transactions"},
     *     operationId="transaction_cancel",
     *     summary="Cancel a single transaction by id",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer access_token ( Obtained after logging in )",
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
    public function refund($id)
    {
        try {

            $transaction = null;

            //Check if the given id corresponds to that of at least one of the created transactions

            if($transaction = Transaction::find($id))
            {
                //check if the transaction has not already been refunded

                if($transaction->type != 3)
                {

                    //Calculate the user's balance based on the type of transaction that has been refunded

                    $newBalance = $transaction->type == 1 ? ($transaction->user->profile->balance - $transaction->value) : ($transaction->user->profile->balance + $transaction->value);
                    $transaction->user->profile->balance = $newBalance;
                    $transaction->user->profile->save();


                    //Record the transaction as refund

                    $transaction->type = 3;
                    $transaction->description = '???????????????????? ????????????????????';
                    $transaction->save();


                    //return the refunded transaction with success message

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaction canceled successfully',
                        'transaction' => $transaction
                    ]);

                }else{

                    //Return an error message, because the transaction has already been refunded

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Transaction already canceled',
                    ]);
                }

            }else{

                //Returns a 404 error since the given id does not match that of any transaction

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
            ],$e->getCode());
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

            //Check if the given id corresponds to that of at least one of the created users

            if($user = User::find($id))
            {
                //return all user's transactions

                return response()->json([
                    'transactions' => $user->transactions()
                        ->orderBy('created_at','desc')
                        ->get()
                ]);
            }else{

                //Returns a 404 error since the given id does not match that of any user

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
            ],$e->getCode());
        }
    }
}
