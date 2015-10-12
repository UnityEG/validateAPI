<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\UserFeedBack;
use DB;
use Input;
//
use App\EssentialEntities\Transformers\UserFeedbackTransformer;

class UserFeedbackController extends ApiController {

    protected $UserFeedbackTransformer;

    public function __construct(){//UserFeedbackTransformer $UserFeedbackTransformer) {
        // Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth');
        // 
//        $this->UserFeedbackTransformer = $UserFeedbackTransformer;
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //
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

        $input = Input::all();
        UserFeedback::create(Input::all());

        /*
          $user_id= $request->input('user_id') ;
          $message  = $request->input('message') ;

          $userFeedback = new UserFeedback();
          $userFeedback->user_id= $user_id ;
          $userFeedback->message= $message ;
          $userFeedback->save();
         */
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
        return $user->Feedback;
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
