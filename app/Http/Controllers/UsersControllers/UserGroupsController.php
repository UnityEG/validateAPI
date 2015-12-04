<?php

namespace App\Http\Controllers\UsersControllers;

use App\Http\Controllers\ApiController;
use App\Http\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserGroupsController extends ApiController
{
    
    public function __construct( ) {
        $this->middleware('jwt.auth');
//        todo add jwt.refresh middleware
    }
    
    /**
     * Display all user groups except 'developers' group.
     *
     * @param UserGroup $user_group_model Instance of UserGroup Model
     * @return array
     */
    public function index(UserGroup $user_group_model)
    {
        if ( !JWTAuth::parseToken()->authenticate()->hasRule('user_group_show_all') ) {
            return $this->setStatusCode(403)->respondWithError('Forbidden');
        }//if ( !JWTAuth::parseToken()->authenticate()->hasRule('user_group_show_all') )
        $response = [];
        foreach ( $user_group_model->whereNotIN('group_name', ['developers'])->get() as $user_group_object ) {
            $response["data"][] = $user_group_object->getBeforeStandardArray();
        }
        return $response;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified user group except developers group.
     *
     * @param \App\Http\Models\UserGroup $user_group_model Instance of UserGroup Model
     * @param  int  $id
     * @return Response
     */
    public function show(UserGroup $user_group_model, $id)
    {
        if ( !JWTAuth::parseToken()->authenticate()->hasRule('user_group_show') ) {
            return $this->setStatusCode(403)->respondWithError('Forbidden');
        }//if ( !JWTAuth::parseToken()->authenticate()->hasRule('user_group_show') )
//        todo Modify response with 404 not found in case of user group not found in the database instead of throwing exception
        $user_group_object = $user_group_model->find((int) $id);
        if ( !is_object( $user_group_object ) ) {
            return $this->respondNotFound();
        }
        return ('developers' == $user_group_object->group_name) ? $this->respond("success") : $user_group_object->getStandardJsonFormat();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
