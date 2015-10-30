<?php

namespace App\Http\Controllers;

use App\EssentialEntities\GeneralHelperTools as GeneralHelperTools;
use App\Http\Models\Business;
use App\Http\Requests\Business\StoreBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuth2;
use Tymon\JWTAuth\JWTAuth;

class BusinessController extends ApiController {
//    todo refine BusinessController to remove unused methods
//    todo update documentation of the class (@var, @param and @return)
//    todo apply lazy instantiation by applying method dependency injection

    /**
     * Instance of GeneralHelperTools class
     * @var GeneralHelperTools
     */
    private $GeneralHelperTools;
    
    /**
     * Instance of Business Model
     * @var Business
     */
    private $businessModel;
    
    /**
     * Instance of JWTAuth class
     * @var object
     */
    private $jwtAuth;
    
    public function __construct(
            GeneralHelperTools $general_helper_tools,
            Business $business_model,
            JWTAuth $jwt_auth
    ) {
        $this->middleware( 'jwt.auth' );
//        todo apply jwt.refresh middleware to refresh token every request
        $this->GeneralHelperTools = $general_helper_tools;
        $this->businessModel = $business_model;
        $this->jwtAuth = $jwt_auth;
    }

    /**
     * Display all the business whether it's active or not.
     * @return array
     */
    public function index() {
        if ( !JWTAuth2::parseToken()->authenticate()->hasRule('business_show_all') ) {
            return $this->setStatusCode(403)->respondWithError('Forbidden');
        }//if ( !JWTAuth2::parseToken()->authenticate()->hasRule('business_show_all') )
        $result = [];
        foreach ($this->businessModel->get() as $business_object){
            $result["data"][] = $business_object->getBeforeStandardArray();
        }//foreach ($this->businessModel->get() as $business_object)
        return $result;
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
     * @param  StoreBusinessRequest  $request
     * @return mix
     */
    public function store( StoreBusinessRequest $request ) {
//        todo apply authentication rules in the StoreBusinessRequest class
        $modified_input = $this->prepareDataForStoringHelper( $request->json( "data" ) );
        DB::beginTransaction();
        $created_business_object = $this->businessModel->create($modified_input);
        if ( is_object( $created_business_object ) ) {
            $created_business_object->businessTypes()->attach($modified_input['business_type_ids']);
            $current_user_object = JWTAuth2::parseToken()->authenticate();
            $created_business_object->users()->attach([$current_user_object->id]);
//            get business types array
            foreach($created_business_object->businessTypes()->get(['type']) as $business_type){
                $business_types_array[] = $business_type->type;
            }//foreach($created_business_object->businessTypes()->get(['type']) as $business_type)
            $business_types_array[] = 'customers';
//            get user groups according to business_types array
            $user_groups_objects = \App\Http\Models\UserGroup::whereIn('group_name', $business_types_array)->get(['id']);
//            loop on user_group_objects and attach with current user if not attached
            foreach( $user_groups_objects as $user_group_object){
                ($current_user_object->userGroups()->where('user_group_id', $user_group_object->id)->exists()) ? : $current_user_object->userGroups()->attach([$user_group_object->id]);
            }//foreach( $user_groups_objects as $user_group_object)
            DB::commit();
            $response = $created_business_object->getStandardJsonFormat();
        }else{
            DB::rollBack();
            $response = $this->respondInternalError();
        }//if ( is_object( $created_business_object ) )
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function show($id ) {
        $current_user_object = JWTAuth2::parseToken()->authenticate();
        if ( (!$current_user_object->business()->where('business_id', (int)$id)->exists()) && (!$current_user_object->hasRule('business_show')) ) {
            return $this->setStatusCode(403)->respondWithError('Forbidden');
        }//if((!$current_user_object->business()->where('business_id', (int)$id)->exists())&&(!$current_user_object->hasRule('business_show')))
        return $this->businessModel->findOrFail($id)->getStandardJsonFormat();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit( $id ) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Business\UpdateBusinessRequest  $request
     * @param  int  $id
     * @return mix array|Json
     */
    public function update( UpdateBusinessRequest $request, $id ) {
//        todo Modify authenticate method in UpdateBusinessRequest class to apply authentication rules
        $business_object = $this->businessModel->findOrFail($id);
        $modified_input = $this->prepareDataForUpdatingHelper($request->json("data"));
        DB::beginTransaction();
        if ( $business_object->update($modified_input) ) {
            $business_object->businessTypes()->sync($modified_input['business_type_ids']);
//            todo update relationships between business and users
            DB::commit();
            return $business_object->getStandardJsonFormat();
        }//if ( $business_object->update($modified_input) )
        return $this->respondInternalError();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy( $id ) {
        //
    }
    
//    todo create showAllActiveBusiness method
//    todo create showActiveBusiness method
    
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
          $modified_input['is_active'] = FALSE;
          $modified_input['is_featured'] = FALSE;
          $modified_input['is_display'] = TRUE;
          return $modified_input;
    }
    
    /**
     * Prepare Data for update method
     * @param array $raw_input
     * @return array
     */
    private function prepareDataForUpdatingHelper( array $raw_input ) {
        (!$logo_id = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'logo_id')) ?  : $modified_input['logo_id'] = (int)$logo_id;
        (!$city_id = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'city_id')) ?  : $modified_input['city_id'] = (int)$city_id;
        (!$region_id = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'region_id')) ?  : $modified_input['region_id'] = (int)$region_id;
        (!$town_id = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'town_id')) ?  : $modified_input['town_id'] = (int)$town_id;
        (!$postcode_id = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'postcode_id')) ?  : $modified_input['postcode_id'] = (int)$postcode_id;
        (!$industry_id = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'industry_id')) ?  : $modified_input['industry_id'] = (int)$industry_id;
        (!$business_type_ids = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'business_type_ids')) ? : $modified_input['business_type_ids'] = array_map( 'intval', $business_type_ids);
        $is_active = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'is_active');
        if ( $is_active ) {
            $modified_input['is_active'] = ("false" !== $is_active) ? TRUE : FALSE;
        }//if ( $is_active )
        (!$business_name = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'business_name')) ?  : $modified_input['business_name'] = (string)$business_name;
        (!$trading_name = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'trading_name')) ?  : $modified_input['tranding_name'] = (string)$trading_name;
        (!$address1 = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'address1')) ?  : $modified_input['address1'] = (string)$address1;
        (!$address2 = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'address2')) ?  : $modified_input['address2'] = (string)$address2;
        (!$phone = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'phone')) ?  : $modified_input['phone'] = (string)$phone;
        (!$website = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'website')) ?  : $modified_input['website'] = (string)$website;
        (!$business_email = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'business_email')) ?  : $modified_input['business_email'] = (string)$business_email;
        (!$contact_name = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'contact_name')) ?  : $modified_input['contact_name'] = (string)$contact_name;
        (!$mobile = $this->GeneralHelperTools->arrayKeySearchRecursively( $raw_input, 'mobile')) ?  : $modified_input['mobile'] = (string)$mobile;
        $is_featured = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'is_featured');
        if ( $is_featured ) {
            $modified_input['is_featured'] = ("false" !== $is_featured) ? TRUE : FALSE;
        }//if ( $is_featured )
        $is_display = $this->GeneralHelperTools->arrayKeySearchRecursively($raw_input, 'is_display');
        if ( $is_display ) {
            $modified_input['is_display'] = ("false" !== $is_display) ? TRUE : FALSE;
        }
        return $modified_input;
    }
}
