<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\aaa\Transformers\BusinessTransformer;
use App\aaa\Transformers\CityTransformer;
use App\aaa\Transformers\RegionTransformer;
use App\aaa\Transformers\TownTransformer;
use App\aaa\Transformers\PostcodeTransformer;
use App\aaa\Transformers\IndustryTransformer;
use App\aaa\Transformers\BusinessTypesTransformer;
use App\aaa\Transformers\UserTransformer;

class Business extends Model {
    
    protected $table = 'business';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'logo_id',
        'city_id',
        'region_id',
        'town_id',
        'postcode_id',
        'industry_id',
        'facebook_page_id',
        'is_active',
        'business_name',
        'trading_name',
        'address1',
        'address2',
        'phone',
        'website',
        'business_email',
        'contact_name',
        'contact_mobile',
        'is_featured',
        'is_display'
    ];
    
    /**
     * Relationship between Business Model and User Model (many to many)
     * @return object
     */
    public function users() {
        return $this->belongsToMany('App\User', 'users_business_rel', 'business_id', 'user_id');
    }
    
    /**
     * 
     * @return object
     */
    public function GiftVoucherParameter() {
        return $this->hasMany('App\Http\Models\GiftVoucherParameter', 'MerchantID');
    }
    
    /**
     * Relationship between Business Model and VoucherParameter Model (one to many)
     * @return object
     */
    public function voucherParameter( ) {
        return $this->hasMany('App\Http\Models\VoucherParameter', 'business_id', 'id');
    }
    
    /**
     * Relationship between Business Model and VoucherValidationLog Model (one to many)
     * @return object
     */
    public function voucherValidationLogs( ) {
        return $this->hasMany('App\Http\VoucherValidationLog', 'business_id', 'id');
    }
    
    /**
     * Relationship between Business Model and BusinessLogo Model (one to many)
     * @return object
     */
    public function businessLogos( ) {
        return $this->hasMany('App\Http\Models\BusinessLogo', 'business_id', 'id');
    }
    
    /**
     * Relationship between Business Model and BusinessType Model (many to many)
     * @return object
     */
    public function businessTypes() {
        return $this->belongsToMany('App\Http\Models\BusinessType', 'business_business_types_rel', 'business_id', 'business_type_id');
    }
    
    /**
     * Relationship between Business Model and City Model (many to one)
     * @return object
     */
    public function city( ) {
        return $this->belongsTo('App\Http\Models\City', 'city_id', 'id');
    }
    
    /**
     * Relationship between Business Model and Region Model (many to one)
     * @return object
     */
    public function region( ) {
        return $this->belongsTo('App\Http\Models\Region', 'region_id', 'id');
    }
    
    /**
     * Relationship between Business Model and Town Model (many to one)
     * @return object
     */
    public function town( ) {
        return $this->belongsTo('App\Http\Models\Town', 'town_id', 'id');
    }
    
    /**
     * Relationship between Business Model and Postcode Model (many to one)
     * @return object
     */
    public function postcode( ) {
        return $this->belongsTo('App\Http\Models\Postcode', 'postcode_id', 'id');
    }
    
    /**
     * Relationship between Business Model and Industry Model (many to one)
     * @return object
     */
    public function industry( ) {
        return $this->belongsTo('App\Http\Models\Industry', 'industry_id', 'id');
    }
//    Helpers
    
    /**
     * Get Active logo object for the business
     * @return object
     */
    public function getActiveLogo( ) {
        return $this->businessLogos()->where('id', $this->logo_id)->first();
    }
    
    public function prepareBusinessGreedyData( ) {
//        todo refine this method and make it like prepareUserGreedyData in User Model
        $city_transformer = new   CityTransformer;
        $region_transformer = new  RegionTransformer ;
        $town_transformer = new  TownTransformer ;
        $postcode_transformer = new  PostcodeTransformer;
        $industry_transformer = new  IndustryTransformer;
        $business_types_transformer = new  BusinessTypesTransformer;
        $user_transformer = new UserTransformer();
        
        $business_array = $this->load('businessLogos', 'city', 'region', 'town', 'postcode', 'industry', 'businessTypes', 'users')->toArray();
        $business_array['city'] = $city_transformer->transform($business_array['city']);
        $business_array['region'] = $region_transformer->transform($business_array['region']);
        $business_array['town'] = $town_transformer->transform($business_array['town']);
        $business_array['postcode'] = $postcode_transformer->transform($business_array['postcode']);
        $business_array['industry'] = $industry_transformer->transform($business_array['industry']);
        $business_array['business_types'] = $business_types_transformer->transformCollection($business_array['business_types']);
        $business_array['users'] = $user_transformer->transformCollection($business_array['users']);
        
//        todo create BusinessLogosTransformer class
        return $business_array;
    }
    
    public function getStandardJsonTransform( ) {
//        todo modify this method
        $business_transformer = new BusinessTransformer;
        return $business_transformer->transform($this->getGreedyBusinessDataArray());
    }
    
    public function getBeforeStandardJson( ) {
//        todo modify this method
        $business_transformer = new BusinessTransformer;
        return $business_transformer->beforeStandard($this->getGreedyBusinessDataArray());
    }
}
