<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Models\Lesson;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\Lesson as LessonResources;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Lesson controller
 * @package App\Http\Controllers
 * @author Alexander Kalksov <munlightshadow@gmail.com>
 */
class LessonController extends Controller
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/lessons",
     *     tags={"Lessons"},
     *     summary="Get a list of lessons",
     *     operationId="getLessons",
     *
     *     @OA\Parameter(
     *          name="sort",
     *          in="query",
     *          description="Sorting column. Use 'id','title','description'. 'id' by default.",
     *          required=false,
     *          @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *          name="order",
     *          in="query",
     *          description="Sorting order. Use 'asc' or 'desc'. 'asc' by default.",
     *          required=false,
     *          @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *          name="countOnPage",
     *          in="query",
     *          description="Rows count on the page. 10 rows by default.",
     *          required=false,
     *          @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number. 1 by default.",
     *          required=false,
     *          @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/LessonRequest")
     *              )
     *          )
     *     )
     * )
     *
     * @param LessonRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(LessonRequest $request)
    {
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'asc');
        $countOnPage = $request->get('countOnPage', 10);

        $query = Lesson::orderBy($sort, $order);

        return LessonResources::collection($query->paginate($countOnPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/lessons",
     *     summary="Add a new lesson",
     *     tags={"Lessons"},
     *     operationId="addLessons",
     *
     *     @OA\RequestBody(
     *         description="New lessons",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/LessonRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data", type="object", ref="#/components/schemas/LessonRequest"
     *              )
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *
     *     security={
     *          {"bearerAuth": {}}
     *     }
     * )
     *
     * @param  LessonRequest $request
     * @return LessonResources
     */
    public function store(LessonRequest $request)
    {
        $data = Lesson::create($request->validated());
        return new LessonResources($data);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/lessons/{id}",
     *     summary="Get a specified lesson",
     *     tags={"Lessons"},
     *     operationId="showLessons",
     *
     *     @OA\Parameter(
     *          name="id",
     *          description="lesson ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data", type="object", ref="#/components/schemas/LessonRequest"
     *              )
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(ref="#/components/schemas/Message")
     *     )
     * )
     *
     * @param  Lesson $lesson
     * @return LessonResources
     */
    public function show(Lesson $lesson)
    {
        return new LessonResources($lesson);
    }

    /**
     * Update the specified resource in storage.
     *
     * * @OA\Put(
     *     path="/lessons/{id}",
     *     summary="Update an existing lesson",
     *     tags={"Lessons"},
     *     operationId="updateLessons",
     *
     *     @OA\Parameter(
     *          name="id",
     *          description="Lessons ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Updated data",
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/LessonRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data", type="object", ref="#/components/schemas/LessonRequest"
     *              )
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
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
     *     security={
     *          {"bearerAuth": {}}
     *     }
     * )
     *
     * @param  LessonRequest $request
     * @param  Lesson $lesson
     * @return LessonResources
     */
    public function update(LessonRequest $request, Lesson $lesson)
    {
        $lesson->update($request->only(['title', 'description']));
        return new LessonResources($lesson);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/lessons/{id}",
     *     summary="Delete a lesson",
     *     tags={"Lessons"},
     *     operationId="deleteLessons",
     *
     *     @OA\Parameter(
     *          name="id",
     *          description="Lesson ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *          response=204,
     *          description="No Content"
     *     ),
     *
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
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
     *     security={
     *          {"bearerAuth": {}}
     *     }
     * )
     *
     * @param Lesson $lesson
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return response()->json(null, 204);
    }
}
