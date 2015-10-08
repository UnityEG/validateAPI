<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\aaa\g as GeneralHelpers;
use App\Http\Requests\Business\StoreBusinessRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Business;
use Tymon\JWTAuth\JWTAuth;
use App\aaa\Transformers\BusinessTransformer;
use App\aaa\Transformers\CityTransformer;
use App\aaa\Transformers\RegionTransformer;
use App\aaa\Transformers\TownTransformer;
use App\aaa\Transformers\PostcodeTransformer;
use App\aaa\Transformers\IndustryTransformer;
use App\aaa\Transformers\BusinessTypesTransformer;
use App\Http\Controllers\UsersController;

class BusinessController extends ApiController {
//todo refine BusinessController to remove unused objects
    /**
     * Instance of g class
     * @var object
     */
    private $generalHelpers;
    
    /**
     * Instance of Business Model
     * @var object
     */
    private $businessModel;
    
    /**
     * Instance of JWTAuth class
     * @var object
     */
    private $jwtAuth;
    
    /**
     * Instance of BusinessTransformer class
     * @var object
     */
    private $businessTransformer;
    
    /**
     * Instance of CityTransformer class
     * @var object
     */
    private $cityTransformer;
    
    /**
     * Instance of RegionTransformer class
     * @var object
     */
    private $regionTransformer;
    
    /**
     * Instance of TownTransformer class
     * @var object
     */
    private $townTransformer;
    
    /**
     * Instance of PostcodeTransformer class
     * @var object
     */
    private $postcodeTransformer;
    
    /**
     *
     * @var object
     */
    private $industryTransformer;
    
    /**
     * Instance of BusinessTypesTransformer class
     * @var object
     */
    private $businessTypesTransformer;
    
    /**
     * Instance of UsersController class
     * @var object
     */
    private $usersController;

    public function __construct(
            GeneralHelpers $general_helpers,
            Business $business_model,
            JWTAuth $jwt_auth,
            BusinessTransformer $business_transformer,
            CityTransformer $city_transformer,
            RegionTransformer $region_transformer,
            TownTransformer $town_transformer,
            PostcodeTransformer $postcode_transformer,
            IndustryTransformer $industry_transformer,
            BusinessTypesTransformer $business_types_transformer,
            UsersController $users_controller
    ) {
        $this->middleware( 'jwt.auth' );
        $this->generalHelpers = $general_helpers;
        $this->businessModel = $business_model;
        $this->jwtAuth = $jwt_auth;
        $this->businessTransformer = $business_transformer;
        $this->cityTransformer = $city_transformer;
        $this->regionTransformer = $region_transformer;
        $this->townTransformer = $town_transformer;
        $this->postcodeTransformer = $postcode_transformer;
        $this->industryTransformer = $industry_transformer;
        $this->businessTypesTransformer = $business_types_transformer;
        $this->usersController = $users_controller;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $business_objects = $this->businessModel->get();
        $result = [];
        foreach ($business_objects as $business_object){
            $result["data"][] = $business_object->getBeforeStandardJson();
        }
        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( StoreBusinessRequest $request ) {
        $modified_input = $this->prepareDataForStoringHelper( $request->json( "data" ) );
        DB::beginTransaction();
        $created_business_object = $this->businessModel->create($modified_input);
        if ( is_object( $created_business_object ) ) {
            $created_business_object->businessTypes()->attach($modified_input['business_type_ids']);
            $current_user_id = $this->jwtAuth->parseToken()->authenticate()->id;
            $created_business_object->users()->attach([$current_user_id]);
            DB::commit();
            $response = $created_business_object->getStandardJsonTransform();
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
     * @return \Illuminate\Http\Response
     */
    public function show( $id ) {
        return $this->businessModel->findOrFail($id)->getStandardJsonTransform();
//        $business_array = $this->businessModel->with('city', 'region', 'town', 'postcode', 'industry', 'businessTypes', 'users')->findOrFail((int)$id)->toArray();
//        $business_array['city'] = $this->cityTransformer->transform($business_array['city']);
//        $business_array['region'] = $this->regionTransformer->transform($business_array['region']);
//        $business_array['town'] = $this->townTransformer->transform($business_array['town']);
//        $business_array['postcode'] = $this->postcodeTransformer->transform($business_array['postcode']);
//        $business_array['industry'] = $this->industryTransformer->transform($business_array['industry']);
//        $business_array['business_types'] = $this->businessTypesTransformer->transformCollection($business_array['business_types']);
//        todo create BusinessLogosTransformer class
//        return $this->businessTransformer->transform($business_array);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit( $id ) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $id ) {
        //todo create update method
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id ) {
        //
    }
    
//    Helpers
    
    public function prepareDataForStoringHelper( array $raw_input ) {
        $modified_input[ 'logo_id' ] = (int)$this->generalHelpers->arrayKeySearchRecursively($raw_input, 'logo_id');
        $modified_input['city_id'] = (int)  $this->generalHelpers->arrayKeySearchRecursively($raw_input, 'city_id');
        $modified_input['region_id'] = (int)$this->generalHelpers->arrayKeySearchRecursively($raw_input, 'region_id');
        $modified_input['town_id'] = (int)  $this->generalHelpers->arrayKeySearchRecursively($raw_input, 'town_id');
        $modified_input['postcode_id'] = (int)  $this->generalHelpers->arrayKeySearchRecursively($raw_input, 'postcode_id');
        $modified_input['industry_id'] = (int)  $this->generalHelpers->arrayKeySearchRecursively($raw_input, 'industry_id');
        $modified_input['business_type_ids'] = array_map('intval', $this->generalHelpers->arrayKeySearchRecursively($raw_input, 'business_type_ids'));
        $modified_input['business_name'] = (string)  $this->generalHelpers->arrayKeySearchRecursively($raw_input, 'business_name');
        $modified_input['trading_name'] = (string)  $this->generalHelpers->arrayKeySearchRecursively($raw_input, 'trading_name');
        $modified_input['address1'] = (string)  $this->generalHelpers->arrayKeySearchRecursively($raw_input, 'address1');
        ($address2 = $this->generalHelpers->arrayKeySearchRecursively( $raw_input, 'address2')) ? $modified_input['address2'] = (string)$address2 : FALSE;
        ($phone = $this->generalHelpers->arrayKeySearchRecursively( $raw_input, 'phone')) ? $modified_input['phone'] = (string)$phone : FALSE;
        ($website = $this->generalHelpers->arrayKeySearchRecursively( $raw_input, 'website')) ? $modified_input['website'] = (string)$website : FALSE;
        ($business_email = $this->generalHelpers->arrayKeySearchRecursively( $raw_input, 'business_email')) ? $modified_input['business_email'] = (string)$business_email : FALSE;
        ($contact_name = $this->generalHelpers->arrayKeySearchRecursively( $raw_input, 'contact_name')) ? $modified_input['contact_name'] = (string)$contact_name : FALSE;
        ($contact_mobile = $this->generalHelpers->arrayKeySearchRecursively( $raw_input, 'contact_mobile')) ? $modified_input['contact_mobile'] = (string)$contact_mobile : FALSE;
          $modified_input['is_active'] = FALSE;
          $modified_input['is_featured'] = FALSE;
          $modified_input['is_display'] = TRUE;
          return $modified_input;
    }

}
