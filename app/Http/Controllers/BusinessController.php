<?php

namespace App\Http\Controllers;

use App\EssentialEntities\GeneralHelperTools\GeneralHelperTools;
use App\Http\Models\Business;
use App\Http\Models\UserGroup;
use App\Http\Requests\Business\AcceptCreateBusinessRequest;
use App\Http\Requests\Business\StoreBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;
use DB;
use JWTAuth;

class BusinessController extends ApiController {

    /**
     * Instance of Business Model
     * @var Business
     */
    private $BusinessModel;
    
    private $GeneralHelperTools;
    
    /**
     * 
     * @param Business $business_model
     */
    public function __construct(Business $business_model, GeneralHelperTools $general_helper_tools) {
        $this->middleware( 'jwt.auth', ['except'=>['showDisplayBusiness', 'listPartners', 'listFeatured']] );
//        todo apply jwt.refresh middleware to refresh token every request
        $this->BusinessModel = $business_model;
        $this->GeneralHelperTools = $general_helper_tools;
    }

    /**
     * Display all the business whether it's active or not.
     * @return array
     */
    public function index() {
        if ( !JWTAuth::parseToken()->authenticate()->hasRule('business_show_all') ) {
            return $this->setStatusCode(403)->respondWithError('Forbidden');
        }//if ( !JWTAuth2::parseToken()->authenticate()->hasRule('business_show_all') )
        return $this->BusinessModel->getStandardJsonCollection();
    }
    
    /**
     * List Business who meet the conditions ( is_active=>1, is_display=>1 )
     * @return array
     */
    public function listPartners(){
        $response["data"] = [];
        foreach ( $this->BusinessModel->where(['is_active'=>1, 'is_display'=>1])->get() as $business_object) {
            $response["data"][] = $business_object->getBeforeStandardArray();
        }//foreach ($this->BusinessModel->where(['is_active'=>1, 'is_display'=>1])->get() as $business_object)
        return $response;
    }
    
    /**
     * List Featured Business who meet these conditions (is_active=>1, is_display=>1, is_featured=>1)
     * @return array
     */
    public function listFeatured(){
        $response["data"] = [];
        foreach($this->BusinessModel->where(['is_active'=>1, 'is_display'=>1, 'is_featured'=>1])->get() as $business_object){
            $response["data"][] = $business_object->getBeforeStandardArray();
        }//foreach($this->BusinessModel->where(['is_active'=>1, 'is_display'=>1, 'is_featured'=>1])->get() as $business_object)
        return $response;
    }
    
    /**
     * List all new businesses with contition (is_new => 1)
     * @param Business $business_model
     * @return array
     */
    public function listCreateRequest( Business $business_model ) {
        $response["data"] = [];
        $business_objects = $business_model->where('is_new', 1)->get();
        foreach ( $business_objects as $business_object) {
            $response["data"][] = $business_object->getBeforeStandardArray();
        }
        return $response;
    } 
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function show($id ) {
        $current_user_object = JWTAuth::parseToken()->authenticate();
        if ( (!$current_user_object->business()->where('business_id', (int)$id)->exists()) && (!$current_user_object->hasRule('business_show')) ) {
            return $this->setStatusCode(403)->respondWithError('Forbidden');
        }//if((!$current_user_object->business()->where('business_id', (int)$id)->exists())&&(!$current_user_object->hasRule('business_show')))
        return $this->BusinessModel->findOrFail($id)->getStandardJsonFormat();
    }
    
    /**
     * Show individual Business who meets the following conditions (is_active=>1, is_display=>1)
     * @param integer $id
     * @param Business $business_model
     * @return array
     */
    public function showDisplayBusiness( $id, Business $business_model) {
        $business_object = $business_model->where(['id'=>(int)$id, 'is_active'=>1, 'is_display'=>1])->first();
        return (is_object( $business_object )) ? $business_object->getStandardJsonFormat() : $this->respondNotFound();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreBusinessRequest  $request
     * @return mix
     */
    public function store( StoreBusinessRequest $request ) {
//        todo apply authentication rules in the StoreBusinessRequest class
        $stored_business = $this->BusinessModel->createNewBusiness($request->json("data"));
        return (is_array( $stored_business ) && array_key_exists( "data", $stored_business)) ? $stored_business : $this->respondWithError( "Faild Creating new Business");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateBusinessRequest  $request
     * @param  int  $id
     * @return array
     */
    public function update( UpdateBusinessRequest $request, $id ) {
//        todo Modify authenticate method in UpdateBusinessRequest class to apply authentication rules
        $business_object = $this->BusinessModel->find((int)$id);
        if ( !is_object( $business_object ) ) {
            return $this->respondNotFound();
        }//if ( !is_object( $business_object ) )
        return ($updated_business_object = $business_object->updateBusiness($request->json("data"))) ? $updated_business_object : $this->respondInternalError( 'Error while updating Business');
    }
    
    /**
     * Accept create business request and activate the business
     * @param integer $id
     * @param AcceptCreateBusinessRequest $request
     * @param \App\User $user_model
     * @return array
     */
    public function acceptCreateRequest($id, AcceptCreateBusinessRequest $request, \App\User $user_model ) {
        $business_object = $this->BusinessModel->find((int)$id);
        if ( !is_object( $business_object ) ) {
            return $this->respondNotFound();
        }//if ( !is_object( $business_object ) )
        $business_object->update(['is_new'=>0, 'is_active'=>1]);
        $user_created_business = $user_model->find((int)$business_object->created_by);
//            get business types array
            foreach($business_object->businessTypes()->get(['type']) as $business_type){
                $user_groups_array[] = $business_type->type.'s';
            }//foreach($created_business_object->businessTypes()->get(['type']) as $business_type)
            $user_groups_array[] = 'customers';
//            get user groups according to business_types array
            $user_groups_objects = UserGroup::whereIn('group_name', $user_groups_array)->get(['id']);
//            get users belongs to
//            loop on user_group_objects and attach with user created the business if not attached
            foreach( $user_groups_objects as $user_group_object){
                ($user_created_business->userGroups()->where('user_group_id', $user_group_object->id)->exists()) ? : $user_created_business->userGroups()->attach([$user_group_object->id]);
            }//foreach( $user_groups_objects as $user_group_object)
            return $business_object->getStandardJsonFormat();
    }
}
