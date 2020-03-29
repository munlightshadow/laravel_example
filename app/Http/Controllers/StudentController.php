<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StudentRequest;
use App\Models\Student;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\Student as StudentResources;
use Symfony\Component\HttpFoundation\JsonResponse;

class StudentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/student",
     *     tags={"Student"},
     *     summary="Get a list of Student",
     *     operationId="getStudent",
     *
     *     @OA\Parameter(
     *          name="sort",
     *          in="query",
     *          description="Sorting column.",
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
     *                  @OA\Items(ref="#/components/schemas/StudentRequest")
     *              )
     *          )
     *     ),
     *
     *     security={
     *          {"bearerAuth": {}}
     *     }     
     * )
     *
     * @param LessonRequest $request
     * @return AnonymousResourceCollection
     */    
    public function index(StudentRequest $request)
    {
        $request->user()->authorizeRoles(['admin', 'user']);

        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'asc');
        $countOnPage = $request->get('countOnPage', 10);

        $query = Student::orderBy($sort, $order);

        return StudentResources::collection($query->paginate($countOnPage));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/student",
     *     summary="Add a new student",
     *     tags={"Student"},
     *     operationId="addStudent",
     *
     *     @OA\RequestBody(
     *         description="New student",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/StudentRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data", type="object", ref="#/components/schemas/StudentRequest"
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
     * @param  StudentRequest $request
     * @return StudentResources
     */    
    public function store(StudentRequest $request)
    {
        $request->user()->authorizeRoles(['admin']);

        $data = Student::create($request->validated());
        return new StudentResources($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/student/{id}",
     *     summary="Get a specified student",
     *     tags={"Student"},
     *     operationId="showStudent",
     *
     *     @OA\Parameter(
     *          name="id",
     *          description="Student ID",
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
     *                  property="data", type="object", ref="#/components/schemas/StudentRequest"
     *              )
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *
     *     security={
     *          {"bearerAuth": {}}
     *     }     
     * )
     *
     *
     * @param  Student $student
     * @return StudentRequest
     */    
    public function show(StudentRequest $request, Student $student)
    {
        $request->user()->authorizeRoles(['admin', 'user']);

        return new StudentResources($student);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * * @OA\Put(
     *     path="/student/{id}",
     *     summary="Update an existing student",
     *     tags={"Student"},
     *     operationId="updateStudent",
     *
     *     @OA\Parameter(
     *          name="id",
     *          description="Student ID",
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
     *             @OA\Schema(ref="#/components/schemas/StudentRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data", type="object", ref="#/components/schemas/StudentRequest"
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
     * @param  StudentRequest $request
     * @param  Student $student
     * @return StudentResources
     */
    public function update(StudentRequest $request, Student $student)
    {
        $request->user()->authorizeRoles(['admin']);

        $student->update($request->only(['name', 'last_name', 'phone', 'email']));
        return new StudentResources($student);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/student/{id}",
     *     summary="Delete a student",
     *     tags={"Student"},
     *     operationId="deleteStudent",
     *
     *     @OA\Parameter(
     *          name="id",
     *          description="Student ID",
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
     * @param Student $student
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(StudentRequest $request, Student $student)
    {
        $request->user()->authorizeRoles(['admin']);

        $student->delete();
        return response()->json(null, 204);
    }
}
