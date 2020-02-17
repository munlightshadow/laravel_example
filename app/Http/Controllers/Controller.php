<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controller class
 *
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel Example",
 *     description="API for Laravel Example"
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @OA\Schema(
     *     schema="Error",
     *     type="object",
     *     @OA\Property(property="message", type="string"),
     *     @OA\Property(
     *          property="errors",
     *          type="object",
     *          @OA\Property(
     *              property="title",
     *              type="array",
     *              @OA\Items(type="string")
     *          )
     *     ),
     * )
     */

    /**
     * @OA\Schema(
     *   schema="Message",
     *   type="object",
     *   @OA\Property(property="message", type="string")
     * )
     */
}