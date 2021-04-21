<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Swagger\Annotations as SWG;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * @SWG\Post(
     *     path="/register",
     *     tags={"Authentification"},
     *     operationId="register",
     *     summary="Register new user",
     *     @SWG\Parameter(
     *         name="name",
     *         description="User fullname",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         format="varchar"
     *     ),
     *    @SWG\Parameter(
     *         name="email",
     *         description="User email",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         format="email"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         description="User password",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         format="password"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="User created successfully"
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
     */

    /**
     * Create a new user
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {

        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        Profile::create([
            'user_id' => $user->id,
            'balance' => 0.0
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


    /**
     * @SWG\Post(
     *     path="/login",
     *     tags={"Authentification"},
     *     operationId="login",
     *     summary="Log in user",
     *    @SWG\Parameter(
     *         name="email",
     *         description="User email",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         format="email"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         description="User password",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         format="password"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="User logged successfully"
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
     */

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $this->validate($request,[
           'email' => 'required|email',
           'password' => 'required'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @SWG\Get(
     *     path="/profile",
     *     tags={"Authentification"},
     *     operationId="profile",
     *     summary="Authebticated user profile",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer access_token ( Obtained after logging in )",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="User profile"
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
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user()->load('profile'));
    }

    /**
     * @SWG\Post(
     *     path="/logout",
     *     tags={"Authentification"},
     *     operationId="logout",
     *     summary="Log out user",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         description="Bearer access_token ( Obtained after logging in )",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="User logged out successfully"
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
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
