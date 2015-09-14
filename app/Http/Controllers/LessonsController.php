<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use App\Lesson;
use App\aaa\Transformers\LessonTransformer;

class LessonsController extends ApiController {

    protected $LessonTransformer;

    public function __construct(LessonTransformer $LessonTransformer) {
        // Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth');
        // 
        $this->LessonTransformer = $LessonTransformer;
//        $this->middleware('auth.basic');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //
        // return Lesson::All(); // really bad practice
        // 1. all is a huge number of data
        // 2. no way to attach meta data
        // 3. database structure is seen to the world
        // 4. any change the database fields name will breakdown the api
        // 5. no way to send the headers/response codes

        $lessons = Lesson::All();

        return $this->respond([
                    'data' => $this->LessonTransformer->transformCollection($lessons->toArray()), // $lessons->all() equals to $lessons->toArray()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
        $lesson = Lesson::find($id);

        //
        if (!$lesson) {
            // 'Lesson does not exist'
            return $this->respondNotFound('Lesson does not exist');
        }

        return $this->respond([
                    'data' => $this->LessonTransformer->transform($lesson),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

}
