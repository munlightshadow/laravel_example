<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Models\User;
use App\Models\Role;

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
        $this->middleware('auth:api', ['except' => ['login', 'registration', 'refresh']]);
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

        $user
           ->roles()
           ->attach(Role::where('name', 'user')->first());        

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
     *                  @OA\Property(property="email", type="string", example="admin@test.com"),
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
     * User info
     *
     * @OA\Post(
     *     path="/auth/me",
     *     security={{"bearerAuth":{}}},
     *     summary="User info",
     *     tags={"Authentication"},
     *     operationId="me",
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
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */  
    public function me(Request $request)
    {
        $request->user()->authorizeRoles(['admin', 'user']);

        return new UserResources($this->guard()->user());
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
     *     @OA\RequestBody(
     *         description="Refreshy token",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="refresh_token", type="string", example="token"),
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
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);

        $data = $request->only('refresh_token');
        $refreshToken = $data['refresh_token'];

        if($user_id = Cache::get('refresh_token.'.$refreshToken)){

            $user = User::findOrFail($user_id);
            $this->guard()->login($user);

            Cache::forget('refresh_token.'.$refreshToken);

            return $this->respondWithToken($this->guard()->refresh());

        };
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the token response
     *
     * @param $token
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        do {
            $refresh_token = bcrypt(str_random(60));
        } while (Cache::has('refresh_token.'.$refresh_token));

        Cache::put('refresh_token.'.$refresh_token, auth()->id(), config('jwt.refresh_ttl'));

        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refresh_token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => new UserResources(auth()->user()),
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
