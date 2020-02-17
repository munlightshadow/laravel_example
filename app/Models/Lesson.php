<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Lesson model
 * @package App\Models
 * @author Alexander Kalskov <munlightshadow@gmail.com>
 *
 * @OA\Schema(
 *   schema="LessonRequest",
 *   type="object",
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="description", type="string"),
 * )
 *
 * @OA\Schema(
 *   schema="LessonResponse",
 *   type="object",
 *   @OA\Property(property="id", format="int32", type="integer"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="description", type="string"),
 * )
 */
class Lesson extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['title', 'description'];

    /**
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];
}




