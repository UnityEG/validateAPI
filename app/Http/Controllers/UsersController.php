<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use App\User;
use App\aaa\Transformers\UserTransformer;

class UsersController extends ApiController {

    protected $UserTransformer;

    public function __construct(UserTransformer $UserTransformer) {
//        todo exclude store method from jwt.auth 
        // Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth', ['except' => 'store']);
        // 
        $this->UserTransformer = $UserTransformer;
//        $this->middleware('auth.basic');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //
        $users = User::All();

        return $this->respond([
                    'data' => $this->UserTransformer->transformCollection($users->toArray()), // $lessons->all() equals to $lessons->toArray()
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
        //todo store new user
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
        $user = User::find($id);

        //
        if (!$user) {
            // Item does not exist
            return $this->respondNotFound('User does not exist');
        }

        return $this->respond([
                    'data' => $this->UserTransformer->transform($user),
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
        //todo update current user
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
    
//    Helpers
//    todo create prepareDataForStoreHelper
//    todo create prepareDataForUpdateHelper
}
