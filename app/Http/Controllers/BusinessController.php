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
        $modified_input = $this->prepareDataForStoringHelper( $request->json( "data" ) );
        DB::beginTransaction();
        $created_business_object = $this->BusinessModel->create($modified_input);
        if ( is_object( $created_business_object ) ) {
            $created_business_object->businessTypes()->attach($modified_input['business_type_ids']);
            $current_user_object = JWTAuth::parseToken()->authenticate();
            $created_business_object->users()->attach([$current_user_object->id]);
            DB::commit();
            $response = $created_business_object->getStandardJsonFormat();
        }else{
            DB::rollBack();
            $response = $this->respondInternalError();
        }//if ( is_object( $created_business_object ) )
        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateBusinessRequest  $request
     * @param  int  $id
     * @return array
     */
    public function update( UpdateBusinessRequest $request, $id, GeneralHelperTools $general_helper_tools ) {
//        todo Modify authenticate method in UpdateBusinessRequest class to apply authentication rules
        $business_object = $this->BusinessModel->findOrFail($id);
        $modified_input = $this->prepareDataForUpdatingHelper($request->json("data"), $general_helper_tools);
        DB::beginTransaction();
        if ( $business_object->update($modified_input) ) {
            (empty($modified_input['business_type_ids'])) ?  : $business_object->businessTypes()->sync($modified_input['business_type_ids']);
//            todo update relationships between business and users
//            todo update relationships between users and user groups according to updated business types
            DB::commit();
            return $business_object->getStandardJsonFormat();
        }//if ( $business_object->update($modified_input) )
        return $this->respondInternalError();
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
    
//    Helpers
    /**
     * Prepare data for store method
     * @param array $raw_input
     * @return array
     */
    private function prepareDataForStoringHelper( array $raw_input ) {
        $modified_input['city_id'] = (int)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'city_id');
        $modified_input['region_id'] = (int)$this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'region_id');
        $modified_input['town_id'] = (int)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'town_id');
        $modified_input['postcode_id'] = (int)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'postcode_id');
        $modified_input['industry_id'] = (int)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'industry_id');
        $modified_input['business_type_ids'] = array_map('intval', $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'business_type_ids'));
        $modified_input['business_name'] = (string)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'business_name');
        $modified_input['trading_name'] = (string)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'trading_name');
        $modified_input['address1'] = (string)  $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'address1');
        ($address2 = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'address2')) ? $modified_input['address2'] = (string)$address2 : FALSE;
        ($phone = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'phone')) ? $modified_input['phone'] = (string)$phone : FALSE;
        ($website = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'website')) ? $modified_input['website'] = (string)$website : FALSE;
        ($business_email = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'business_email')) ? $modified_input['business_email'] = (string)$business_email : FALSE;
        ($contact_name = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'contact_name')) ? $modified_input['contact_name'] = (string)$contact_name : FALSE;
        ($contact_mobile = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'contact_mobile')) ? $modified_input['contact_mobile'] = (string)$contact_mobile : FALSE;
        (!$available_hours_mon = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'available_hours_mon')) ? : $modified_input['available_hours_mon'] = (string)$available_hours_mon;
        (!$available_hours_tue = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'available_hours_tue')) ? : $modified_input['available_hours_tue'] = (string)$available_hours_tue;
        (!$available_hours_wed = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'available_hours_wed')) ? : $modified_input['available_hours_wed'] = (string)$available_hours_wed;
        (!$available_hours_thu = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'available_hours_thu')) ? : $modified_input['available_hours_thu'] = (string)$available_hours_thu;
        (!$available_hours_fri = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'available_hours_fri')) ? : $modified_input['available_hours_fri'] = (string)$available_hours_fri;
        (!$available_hours_sat = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'available_hours_sat')) ? : $modified_input['available_hours_sat'] = (string)$available_hours_sat;
        (!$available_hours_sun = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'available_hours_sun')) ? : $modified_input['available_hours_sun'] = (string)$available_hours_sun;
        $modified_input[ 'is_new' ]            = 1;
        $modified_input[ 'is_active' ]         = 0;
        $modified_input[ 'is_featured' ]       = FALSE;
        $modified_input[ 'is_display' ]        = TRUE;
        $modified_input['code'] = '';
        $modified_input['created_by'] = \JWTAuth::parseToken()->authenticate()->id;
          return $modified_input;
    }
    
    /**
     * Prepare Data for update method
     * @param array $raw_input
     * @return array
     */
    private function prepareDataForUpdatingHelper( array $raw_input, GeneralHelperTools $general_helper_tools ) {
//        todo Modify prepareDataForUpdatingHelper method to suit new changes in Business database table
        (!$logo_id = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'logo_id')) ?  : $modified_input['logo_id'] = (int)$logo_id;
        (!$city_id = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'city_id')) ?  : $modified_input['city_id'] = (int)$city_id;
        (!$region_id = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'region_id')) ?  : $modified_input['region_id'] = (int)$region_id;
        (!$town_id = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'town_id')) ?  : $modified_input['town_id'] = (int)$town_id;
        (!$postcode_id = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'postcode_id')) ?  : $modified_input['postcode_id'] = (int)$postcode_id;
        (!$industry_id = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'industry_id')) ?  : $modified_input['industry_id'] = (int)$industry_id;
        (!$business_type_ids = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'business_type_ids')) ? : $modified_input['business_type_ids'] = array_map( 'intval', $business_type_ids);
        $is_active = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_active');
        if ( $is_active ) {
            $modified_input['is_active'] = ("false" !== $is_active) ? TRUE : FALSE;
        }//if ( $is_active )
        (!$business_name = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'business_name')) ?  : $modified_input['business_name'] = (string)$business_name;
        (!$trading_name = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'trading_name')) ?  : $modified_input['tranding_name'] = (string)$trading_name;
        (!$address1 = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'address1')) ?  : $modified_input['address1'] = (string)$address1;
        (!$address2 = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'address2')) ?  : $modified_input['address2'] = (string)$address2;
        (!$phone = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'phone')) ?  : $modified_input['phone'] = (string)$phone;
        (!$website = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'website')) ?  : $modified_input['website'] = (string)$website;
        (!$business_email = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'business_email')) ?  : $modified_input['business_email'] = (string)$business_email;
        (!$contact_name = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'contact_name')) ?  : $modified_input['contact_name'] = (string)$contact_name;
        (!$mobile = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'mobile')) ?  : $modified_input['mobile'] = (string)$mobile;
        $is_featured = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_featured');
        if ( $is_featured ) {
            $modified_input['is_featured'] = ("false" !== $is_featured) ? TRUE : FALSE;
        }//if ( $is_featured )
        $is_display = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_display');
        if ( $is_display ) {
            $modified_input['is_display'] = ("false" !== $is_display) ? TRUE : FALSE;
        }
        return $modified_input;
    }

    

}
