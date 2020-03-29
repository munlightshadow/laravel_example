<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Student model
 * @package App\Models
 * @author Alexander Kalskov <munlightshadow@gmail.com>
 *
 * @OA\Schema(
 *   schema="StudentRequest",
 *   type="object",
 *   @OA\Property(property="name", type="string", example="Jhon"),
 *   @OA\Property(property="last_name", type="string", example="Doe"),
 *   @OA\Property(property="phone", type="string", example="+71112223344"),
 *   @OA\Property(property="email", type="string", example="student@test.com"), 
 * )
 *
 * @OA\Schema(
 *   schema="StudentResponse",
 *   type="object",
 *   @OA\Property(property="id", format="int32", type="integer"),
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="last_name", type="string"),
 *   @OA\Property(property="phone", type="string"),
 *   @OA\Property(property="email", type="string"), 
 * )
 */
class Student extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'last_name', 'phone', 'email'];

    /**
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];
}
