<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends AuthController
{
    use ResetsPasswords;

    private $response;

    /**
     * Class constructor
     */
    public function __construct() {}

    /**
     * Reset the given user password
     *
     * @OA\Post(
     *     path="/auth/reset",
     *     summary="Reset the user password",
     *     tags={"Authentication"},
     *     operationId="resetPassword",
     *
     *     @OA\RequestBody(
     *         description="Reset password data",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="token", type="string"),
     *                  @OA\Property(property="email", type="string", example="user@test.com"),
     *                  @OA\Property(property="password", type="string", example="qweqweqwe"),
     *                  @OA\Property(property="password_confirmation", type="string", example="qweqweqwe")     
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
     *     ),
     *
     *     @OA\Response(
     *          response=502,
     *          description="Bad Gateway",
     *          @OA\JsonContent(ref="#/components/schemas/Message")
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $status = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->response = $this->resetPassword($user, $password);
        });

        return $status == Password::PASSWORD_RESET ?
            $this->response :
            response()->json(['message' => 'Password reset failed'], 502);

    }

    /**
     * Reset the given user's password
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return JsonResponse
     */
    protected function resetPassword($user, $password)
    {
        $user->setPasswordAttribute($password)->save();

        event(new PasswordReset($user));

        return $this->respondWithToken($this->guard()->login($user));
    }
}
