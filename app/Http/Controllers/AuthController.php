<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

use App\Http\Resources\User as UserResources;

/**
 * Auth controller
 * @package App\Http\Controllers
 * @author Alexander Kalksov <munlightshadow@gmail.com>
 */
class AuthController extends Controller
{
    /**
     * @OA\Schema(
     *   schema="AuthToken",
     *   type="object",
     *   @OA\Property(property="token", type="string"),
     *   @OA\Property(property="token_type", type="string"),
     *   @OA\Property(property="expires_in", format="int32", type="integer")
     * )
     */

    /**
     * @OA\SecurityScheme(
     *     name="Authorization",
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuth"
     * )
     */

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registration']]);
    }


    /**
     * User login
     *
     * @OA\Post(
     *     path="/auth/registration",
     *     summary="Register a user",
     *     tags={"Authentication"},
     *     operationId="Register",
     *
     *     @OA\RequestBody(
     *         description="Register credentials",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", example="user"),
     *                  @OA\Property(property="email", type="string", example="user@test.com"),
     *                  @OA\Property(property="password", type="string", example="qweqwe"),
     *                  @OA\Property(property="c_password", type="string", example="qweqwe")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/AuthToken")
     *     ),
     *
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registration(Request $request)
    {        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        $credentials = $request->only('name', 'email', 'password');
        $credentials['password'] = bcrypt($request->password);
        $user = User::create($credentials);

        // $user
        //    ->roles()
        //    ->attach(Role::where('name', $role)->first());

        return new UserResources($user);
    }

    /**
     * User login
     *
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Login a user",
     *     tags={"Authentication"},
     *     operationId="login",
     *
     *     @OA\RequestBody(
     *         description="Login credentials",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="email", type="string", example="user@test.com"),
     *                  @OA\Property(property="password", type="string", example="qweqwe")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/AuthToken")
     *     ),
     *
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * User logout
     *
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Logout the current user",
     *     tags={"Authentication"},
     *     operationId="logout",
     *
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *
     *     security={
     *          {"bearerAuth": {}}
     *     }
     * )
     *
     * @return JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Refresh the token
     *
     * @OA\Post(
     *     path="/auth/refresh",
     *     summary="Refresh the user token",
     *     tags={"Authentication"},
     *     operationId="refreshToken",
     *
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/AuthToken")
     *     ),
     *
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *
     *     security={
     *          {"bearerAuth": {}}
     *     }
     * )
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token response
     *
     * @param $token
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard instance
     *
     * @return mixed
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
