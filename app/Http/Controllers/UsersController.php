<?php

namespace App\Http\Controllers;

use App\EssentialEntities\GeneralHelperTools as GeneralHelperTools;
use App\Http\Requests\Users\StoreUserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Response;
use App\Http\Models\UserGroup;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\ShowUserRequest;

class UsersController extends ApiController {
//    todo refine UserController to remove unused methods
    /**
     *Instance of g class
     * @var object
     */
    private $GeneralHelperTools;
    
    /**
     *Instance of User Model class
     * @var object
     */
    private $UserModel;
    
    /**
     *Instance of UserGroup Model class
     * @var object
     */
    private $UserGroupModel;

    public function __construct(
 GeneralHelperTools $general_helper_tools,
            User $user_model,
            UserGroup $user_group_model
            ) {
        // Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth', ['except' => 'store']);
//        todo apply jwt.refresh middleware to refresh token
//        $this->middleware('jwt.refresh');
        // 
        $this->GeneralHelperTools = $general_helper_tools;
        $this->UserModel = $user_model;
        $this->UserGroupModel = $user_group_model;
//        $this->middleware('auth.basic');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
//        todo create authentication rules to index method
        $data = [];
        foreach ( $this->UserModel->all() as $user_object) {
            $data["data"][] = $user_object->getBeforeStandardArray();
        }
        return $data;
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
    public function store(StoreUserRequest $request) {
        $modified_input = $this->prepareDataForStoringHelper($request->json("data"));
        DB::beginTransaction();
        $created_user = $this->UserModel->create($modified_input);
        if ( is_object( $created_user ) ) {
            $customer_group_object = $this->UserGroupModel->where('group_name', 'customers')->first();
            $customer_group_object->users()->attach([$created_user->id]);
            DB::commit();
            $response = $created_user->getStandardJsonFormat();
        }else{
            DB::rollBack();
            $response = $this->respondInternalError();
        }//if ( is_object( $created_user ) )
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(ShowUserRequest $request, $id) {
        return $this->UserModel->findOrFail($id)->getStandardJsonFormat();
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
    public function update(  UpdateUserRequest $request) {
        $user_object_to_update = $this->UserModel->findOrFail($request->json('data')['id']);
        $modified_input = $this->prepareDataForUpdatingHelper($request->json('data'));
        DB::beginTransaction();
        if ( $user_object_to_update->update($modified_input) ) {
            $user_object_to_update->userGroups()->sync($modified_input['user_group_ids']);
            DB::commit();
            $response = $user_object_to_update->getStandardJsonFormat();
        }else{
            DB::rollBack();
            $response = $this->respondInternalError();
        }
        return $response;
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
    
    /**
     * Prepare data for store method
     * @param array $raw_input
     * @return array
     */
    private function prepareDataForStoringHelper( array $raw_input) {
        $modified_input['city_id'] = (int)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'city_id');
        $modified_input['region_id'] = (int)$this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'region_id');
        $modified_input['town_id'] = (int) $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'town_id');
        $modified_input['postcode_id'] = (int) $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'postcode_id');
        if ( $is_active = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'is_active') ) {
            $modified_input['is_active'] = ("false" === $is_active) ? FALSE : TRUE;
        }else{
            $modified_input['is_active'] = TRUE;
        }//if ( $is_active = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'is_active') )
        $modified_input['email'] = (string)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'email');
        $modified_input['password'] = Hash::make($this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'password'));
        $modified_input['title'] = (string)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'title');
        $modified_input['first_name'] = (string)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'first_name');
        $modified_input['last_name'] = (string)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'last_name');
        ($gender = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'gender')) ? $modified_input['gender'] = (string)$gender : FALSE;
        $modified_input['dob'] = $this->GeneralHelperTools->utcDateTime($this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'dob').' 00:00', 'm/d/Y H:i');
        $modified_input['address1'] = (string)$this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'address1');
        ($address2 = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'address2')) ? $modified_input['address2'] = (string)$address2 : FALSE;
        ($phone = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'phone')) ? $modified_input['phone'] = (int)$phone : FALSE;
        ($mobile = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'mobile')) ? $modified_input['mobile'] = (int)$mobile : FALSE;
        if ( $is_notify_deal = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'is_notify_deal') ) {
            $modified_input['is_notify_deal'] = ("false" === $is_notify_deal) ? FALSE : TRUE;
        }else{
            $modified_input['is_notify_deal'] = TRUE;
        }//if ( $is_notify_deal = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'is_notify_deal') )
        return $modified_input;
    }
    
    /**
     * Prepare data for update method
     * @param array $raw_input
     * @return array $modified_input
     */
    private function prepareDataForUpdatingHelper( array $raw_input) {
        ($city_id = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'city_id')) ? $modified_input['city_id'] = (int)$city_id : FALSE;
        ($region_id = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'region_id')) ? $modified_input['region_id'] = (int)$region_id : FALSE;
        ($town_id = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'town_id')) ? $modified_input['town_id'] = (int)$town_id: FALSE;
        ($postcode_id = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'postcode_id')) ? $modified_input['postcode_id'] = (int)$postcode_id : FALSE;
        if ( $is_active = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'is_active') ) {
            $modified_input['is_active'] = ("false" === $is_active) ? FALSE : TRUE;
        }//if ( $is_active = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'is_active') )
        ($email = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'email')) ? $modified_input['email'] = (string)$email : FALSE;
        ($password = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'password')) ? $modified_input['password'] = Hash::make($password) : FALSE;
        ($title = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'title')) ? $modified_input['title'] = (string)$title : FALSE;
        ($first_name = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'first_name')) ? $modified_input['first_name'] = (string)$first_name : FALSE;
        ($last_name = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'last_name')) ? $modified_input['last_name'] = (string)$last_name : FALSE;
        ($gender = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'gender')) ? $modified_input['gender'] = (string)$gender : FALSE;
        ($dob = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'dob')) ? $modified_input['dob'] = $this->GeneralHelperTools->utcDateTime( $dob.' 00:00', 'd/m/Y H:i') : FALSE;
        ($address1 = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'address1')) ? $modified_input['address1'] = (string)$address1 : FALSE;
        ($address2 = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'address2')) ? $modified_input['address2'] = (string)$address2 : FALSE;
        ($phone = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'phone')) ? $modified_input['phone'] = (string)$phone : FALSE;
        ($mobile = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'mobile')) ? $modified_input['mobile'] = (string)$mobile : FALSE;
        if ( $is_notify_deal = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'is_notify_deal') ) {
            $modified_input['is_notify_deal'] = ("false" === $is_notify_deal) ? FALSE : TRUE;
        }//if ( $is_notify_deal = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'is_notify_deal') )
        ($user_group_ids = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'user_group_ids')) ? $modified_input['user_group_ids'] = array_map( 'intval', $user_group_ids) : FALSE;
        return $modified_input;
    }
    
}
