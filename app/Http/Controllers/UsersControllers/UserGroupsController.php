<?php

namespace App\Http\Controllers\UsersControllers;

use App\Http\Controllers\ApiController;
use App\Http\Models\UserGroup;
use App\Http\Requests\Users\UserGroups\IndexUserGroupRequest;
use App\Http\Requests\Users\UserGroups\ShowUserGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserGroupsController extends ApiController
{
    
    public function __construct( ) {
        $this->middleware('jwt.auth');
//        todo add jwt.refresh middleware
    }
    
    /**
     * Display all user groups except 'developers' group.
     *
     * @param IndexUserGroupRequest $request Instance of IndexUserGroupRequest class
     * @param UserGroup $user_group_model Instance of UserGroup Model
     * @return Response
     */
    public function index( IndexUserGroupRequest $request, UserGroup $user_group_model)
    {
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
     * @param ShowUserGroupRequest $request Instance of ShowUserGroupRequest class
     * @param UserGroup $user_group_model Instance of UserGroup Model
     * @param  int  $id
     * @return Response
     */
    public function show(ShowUserGroupRequest $request, UserGroup $user_group_model, $id)
    {
        $user_group_object = $user_group_model->findOrFail((int) $id);
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
