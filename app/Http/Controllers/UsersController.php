<?php

namespace App\Http\Controllers;

use App\aaa\g;
use App\aaa\Transformers\UserTransformer;
use App\Http\Requests\Users\StoreUserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Response;
use App\Http\Models\UserGroup;
use App\Http\Requests\Users\UpdateUserRequest;

class UsersController extends ApiController {
    
    /**
     * Instance of UserTransformer class
     * @var object
     */
    protected $UserTransformer;
    
    /**
     *Instance of g class
     * @var object
     */
    protected $g;
    
    /**
     *Instance of User Model class
     * @var object
     */
    protected $userModel;
    
    /**
     *Instance of UserGroup Model class
     * @var object
     */
    private $userGroupModel;

    public function __construct(
            UserTransformer $UserTransformer,
            g $g,
            User $user_model,
            UserGroup $user_group_model
            ) {
//        todo exclude store method from jwt.auth 
        // Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth', ['except' => 'store']);
        // 
        $this->UserTransformer = $UserTransformer;
        $this->g = $g;
        $this->userModel = $user_model;
        $this->userGroupModel = $user_group_model;
//        $this->middleware('auth.basic');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $user_arrays_with_greedy_data = $this->userModel->with('city', 'region', 'town', 'postcode', 'userGroups')->get()->toArray();
        return $this->UserTransformer->transformCollection($user_arrays_with_greedy_data);
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
        $created_user = $this->userModel->create($modified_input);
        if ( is_object( $created_user ) ) {
            $customer_group_object = $this->userGroupModel->where('group_name', 'customers')->first();
            $customer_group_object->users()->attach([$created_user->id]);
            DB::commit();
            $created_user = $created_user->load('city', 'region', 'town', 'postcode', 'userGroups');
            $response = $this->UserTransformer->transform($created_user->toArray());
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
    public function show($id) {
        $user_array_with_greedy_data = $this->userModel->with('city', 'region', 'town', 'postcode', 'userGroups')->findOrFail((int)$id)->toArray();
        return $this->UserTransformer->transform($user_array_with_greedy_data);
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
        $user_object_to_update = $this->userModel->findOrFail($request->json('data')['id']);
        $modified_input = $this->prepareDataForUpdatingHelper($request->json('data'));
        DB::beginTransaction();
        if ( $user_object_to_update->update($modified_input) ) {
            $user_object_to_update->userGroups()->sync($modified_input['user_group_ids']);
            DB::commit();
            $response = $this->UserTransformer->transform($user_object_to_update->load('city', 'region', 'town', 'postcode', 'userGroups')->toArray());
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
        $modified_input['city_id'] = (int)  $this->g->arrayKeySearchRecursively($raw_input, 'city_id');
        $modified_input['region_id'] = (int)$this->g->arrayKeySearchRecursively($raw_input, 'region_id');
        $modified_input['town_id'] = (int) $this->g->arrayKeySearchRecursively($raw_input, 'town_id');
        $modified_input['postcode_id'] = (int) $this->g->arrayKeySearchRecursively($raw_input, 'postcode_id');
        if ( $is_active = $this->g->arrayKeySearchRecursively( $raw_input, 'is_active') ) {
            $modified_input['is_active'] = ("false" === $is_active) ? FALSE : TRUE;
        }else{
            $modified_input['is_active'] = TRUE;
        }//if ( $is_active = $this->g->arrayKeySearchRecursively( $raw_input, 'is_active') )
        $modified_input['email'] = (string)  $this->g->arrayKeySearchRecursively($raw_input, 'email');
        $modified_input['password'] = Hash::make($this->g->arrayKeySearchRecursively( $raw_input, 'password'));
        $modified_input['title'] = (string)  $this->g->arrayKeySearchRecursively($raw_input, 'title');
        $modified_input['first_name'] = (string)  $this->g->arrayKeySearchRecursively($raw_input, 'first_name');
        $modified_input['last_name'] = (string)  $this->g->arrayKeySearchRecursively($raw_input, 'last_name');
        ($gender = $this->g->arrayKeySearchRecursively($raw_input, 'gender')) ? $modified_input['gender'] = (string)$gender : FALSE;
        $modified_input['dob'] = $this->g->utcDateTime($this->g->arrayKeySearchRecursively($raw_input, 'dob').' 00:00', 'm/d/Y H:i');
        $modified_input['address1'] = (string)$this->g->arrayKeySearchRecursively($raw_input, 'address1');
        ($address2 = $this->g->arrayKeySearchRecursively($raw_input, 'address2')) ? $modified_input['address2'] = (string)$address2 : FALSE;
        ($phone = $this->g->arrayKeySearchRecursively($raw_input, 'phone')) ? $modified_input['phone'] = (int)$phone : FALSE;
        ($mobile = $this->g->arrayKeySearchRecursively($raw_input, 'mobile')) ? $modified_input['mobile'] = (int)$mobile : FALSE;
        if ( $is_notify_deal = $this->g->arrayKeySearchRecursively( $raw_input, 'is_notify_deal') ) {
            $modified_input['is_notify_deal'] = ("false" === $is_notify_deal) ? FALSE : TRUE;
        }else{
            $modified_input['is_notify_deal'] = TRUE;
        }//if ( $is_notify_deal = $this->g->arrayKeySearchRecursively( $raw_input, 'is_notify_deal') )
        return $modified_input;
    }
    
    /**
     * Prepare data for update method
     * @param array $raw_input
     * @return array $modified_input
     */
    private function prepareDataForUpdatingHelper( array $raw_input) {
        ($city_id = $this->g->arrayKeySearchRecursively($raw_input, 'city_id')) ? $modified_input['city_id'] = (int)$city_id : FALSE;
        ($region_id = $this->g->arrayKeySearchRecursively($raw_input, 'region_id')) ? $modified_input['region_id'] = (int)$region_id : FALSE;
        ($town_id = $this->g->arrayKeySearchRecursively($raw_input, 'town_id')) ? $modified_input['town_id'] = (int)$town_id: FALSE;
        ($postcode_id = $this->g->arrayKeySearchRecursively( $raw_input, 'postcode_id')) ? $modified_input['postcode_id'] = (int)$postcode_id : FALSE;
        if ( $is_active = $this->g->arrayKeySearchRecursively( $raw_input, 'is_active') ) {
            $modified_input['is_active'] = ("false" === $is_active) ? FALSE : TRUE;
        }//if ( $is_active = $this->g->arrayKeySearchRecursively( $raw_input, 'is_active') )
        ($email = $this->g->arrayKeySearchRecursively( $raw_input, 'email')) ? $modified_input['email'] = (string)$email : FALSE;
        ($password = $this->g->arrayKeySearchRecursively( $raw_input, 'password')) ? $modified_input['password'] = Hash::make($password) : FALSE;
        ($title = $this->g->arrayKeySearchRecursively( $raw_input, 'title')) ? $modified_input['title'] = (string)$title : FALSE;
        ($first_name = $this->g->arrayKeySearchRecursively( $raw_input, 'first_name')) ? $modified_input['first_name'] = (string)$first_name : FALSE;
        ($last_name = $this->g->arrayKeySearchRecursively( $raw_input, 'last_name')) ? $modified_input['last_name'] = (string)$last_name : FALSE;
        ($gender = $this->g->arrayKeySearchRecursively( $raw_input, 'gender')) ? $modified_input['gender'] = (string)$gender : FALSE;
        ($dob = $this->g->arrayKeySearchRecursively( $raw_input, 'dob')) ? $modified_input['dob'] = $this->g->utcDateTime( $dob.' 00:00', 'd/m/Y H:i') : FALSE;
        ($address1 = $this->g->arrayKeySearchRecursively( $raw_input, 'address1')) ? $modified_input['address1'] = (string)$address1 : FALSE;
        ($address2 = $this->g->arrayKeySearchRecursively( $raw_input, 'address2')) ? $modified_input['address2'] = (string)$address2 : FALSE;
        ($phone = $this->g->arrayKeySearchRecursively( $raw_input, 'phone')) ? $modified_input['phone'] = (string)$phone : FALSE;
        ($mobile = $this->g->arrayKeySearchRecursively( $raw_input, 'mobile')) ? $modified_input['mobile'] = (string)$mobile : FALSE;
        if ( $is_notify_deal = $this->g->arrayKeySearchRecursively( $raw_input, 'is_notify_deal') ) {
            $modified_input['is_notify_deal'] = ("false" === $is_notify_deal) ? FALSE : TRUE;
        }//if ( $is_notify_deal = $this->g->arrayKeySearchRecursively( $raw_input, 'is_notify_deal') )
        ($user_group_ids = $this->g->arrayKeySearchRecursively( $raw_input, 'user_group_ids')) ? $modified_input['user_group_ids'] = array_map( 'intval', $user_group_ids) : FALSE;
        return $modified_input;
    }
    
}
