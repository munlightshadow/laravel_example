<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForgotPasswordController extends AuthController
{
    use SendsPasswordResetEmails;

    /**
     * Class constructor
     */
    public function __construct() {}

    /**
     * Send recovery information to user email
     *
     * @OA\Post(
     *     path="/auth/recovery",
     *     summary="Recovery the user password",
     *     tags={"Authentication"},
     *     operationId="recoveryPassword",
     *
     *     @OA\RequestBody(
     *         description="User email",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="email", type="string", example="admin@test.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(ref="#/components/schemas/Message")
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
     * @param Request $request
     * @return JsonResponse
     */
    public function recovery(Request $request)
    {
        $this->validateEmail($request);

        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        switch ($response) {
            case PasswordBroker::RESET_LINK_SENT:
                return response()->json(['message' => 'Success']);

            case PasswordBroker::INVALID_USER:
                return response()->json(['message' => 'Invalid user'], 404);

            case PasswordBroker::RESET_THROTTLED:
                return response()->json(['message' => 'Requests throttled'], 502);

            default:
                return response()->json(['message' => 'Email send failed'], 502);

        }
    }
}
