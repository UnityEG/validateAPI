<?php

namespace App\Http\Models;
//todo use Facades instead of instantiating objects from transformers inside methods


use App\EssentialEntities\Transformers\BusinessLogoTransformer;
use App\EssentialEntities\Transformers\BusinessTypesTransformer;
use App\EssentialEntities\Transformers\CityTransformer;
use App\EssentialEntities\Transformers\IndustryTransformer;
use App\EssentialEntities\Transformers\PostcodeTransformer;
use App\EssentialEntities\Transformers\RegionTransformer;
use App\EssentialEntities\Transformers\TownTransformer;
use App\EssentialEntities\Transformers\UserTransformer;
use BusinessTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model {
    
    /**
     * Table name in the database
     * @var string
     */
    protected $table = 'business';
    
    /**
     * Timestamps columns in the database to be instantiated as Carbon objects
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * Whitelist of the fillable columns of the table in the database
     * @var array
     */
    protected $fillable = [
        'logo_id',
        'city_id',
        'region_id',
        'town_id',
        'postcode_id',
        'industry_id',
        'facebook_page_id',
        'code',
        'is_new',
        'is_active',
        'is_display',
        'is_featured',
        'business_name',
        'trading_name',
        'address1',
        'address2',
        'phone',
        'website',
        'business_email',
        'contact_name',
        'contact_mobile',
        'available_hours_mon', 
        'available_hours_tue',
        'available_hours_wed',
        'available_hours_thu',
        'available_hours_fri',
        'available_hours_sat',
        'available_hours_sun',
        'created_by',
    ];
    
    /**
     * Relationship between Business Model and User Model (many to many)
     * @return BelongsToMany
     */
    public function users() {
        return $this->belongsToMany('App\User', 'users_business_rel', 'business_id', 'user_id');
    }
    
    /**
     * Relationship between Business Model and VoucherParameter Model (one to many)
     * @return HasMany
     */
    public function voucherParameter( ) {
        return $this->hasMany('App\Http\Models\VoucherParameter', 'business_id', 'id');
    }
    
    /**
     * Relationship between Business Model and VoucherValidationLog Model (one to many)
     * @return HasMany
     */
    public function voucherValidationLogs( ) {
        return $this->hasMany('App\Http\VoucherValidationLog', 'business_id', 'id');
    }
    
    /**
     * Relationship between Business Model and BusinessLogo Model (one to many)
     * @return HasMany
     */
    public function businessLogos( ) {
        return $this->hasMany('App\Http\Models\BusinessLogo', 'business_id', 'id');
    }
    
    /**
     * Relationship between Business Model and BusinessType Model (many to many)
     * @return BelongsToMany
     */
    public function businessTypes() {
        return $this->belongsToMany('App\Http\Models\BusinessType', 'business_business_types_rel', 'business_id', 'business_type_id');
    }
    
    /**
     * Relationship between Business Model and City Model (many to one)
     * @return BelongsTo
     */
    public function city( ) {
        return $this->belongsTo('App\Http\Models\City', 'city_id', 'id');
    }
    
    /**
     * Relationship between Business Model and Region Model (many to one)
     * @return BelongsTo
     */
    public function region( ) {
        return $this->belongsTo('App\Http\Models\Region', 'region_id', 'id');
    }
    
    /**
     * Relationship between Business Model and Town Model (many to one)
     * @return BelongsTo
     */
    public function town( ) {
        return $this->belongsTo('App\Http\Models\Town', 'town_id', 'id');
    }
    
    /**
     * Relationship between Business Model and Postcode Model (many to one)
     * @return BelongsTo
     */
    public function postcode( ) {
        return $this->belongsTo('App\Http\Models\Postcode', 'postcode_id', 'id');
    }
    
    /**
     * Relationship between Business Model and Industry Model (many to one)
     * @return BelongsTo
     */
    public function industry( ) {
        return $this->belongsTo('App\Http\Models\Industry', 'industry_id', 'id');
    }
//    Helpers
    
    /**
     * Get Active logo object for the business
     * @return BusinessLogo
     */
    public function getActiveLogo( ) {
        return $this->businessLogos()->where('id', $this->logo_id)->first();
    }
    
    /**
     * Prepare Business Data with all its relationships data in a greedy way in standard transform
     * @return array
     */
    public function prepareBusinessGreedyData( ) {
        $business_greedy_array = $this->load(['businessLogos'=> function($query){
//            return with the active logo only when calling business object
            $query->where('id', $this->logo_id);
        }, 'city', 'region', 'town', 'postcode', 'industry', 'businessTypes', 'users'])->toArray();
        (empty($business_greedy_array['business_logos'][0])) ?  : $business_greedy_array['business_logos'] = (new BusinessLogoTransformer())->transform( $business_greedy_array['business_logos'][0]);
        (empty($business_greedy_array['city'])) ?  : $business_greedy_array['city'] = (new CityTransformer())->transform( $business_greedy_array['city']);
        (empty($business_greedy_array['region'])) ?  : $business_greedy_array['region'] = (new RegionTransformer())->transform( $business_greedy_array['region']);
        (empty($business_greedy_array['town'])) ?  : $business_greedy_array['town'] = (new TownTransformer())->transform( $business_greedy_array['town']);
        (empty($business_greedy_array['postcode'])) ?  : $business_greedy_array['postcode'] = (new PostcodeTransformer())->transform( $business_greedy_array['postcode']);
        (empty($business_greedy_array['industry'])) ?  : $business_greedy_array['industry'] = (new IndustryTransformer())->transform( $business_greedy_array['industry']);
        (empty($business_greedy_array['business_types'])) ?  : $business_greedy_array['business_types'] = (new BusinessTypesTransformer())->transformCollection( $business_greedy_array['business_types']);
        (empty($business_greedy_array['users'])) ?  : $business_greedy_array['users'] = (new UserTransformer())->transformCollection( $business_greedy_array['users']);
        return $business_greedy_array;
    }
    
    /**
     * Get Standard Json API Format for single object
     * @return array
     */
    public function getStandardJsonFormat( ) {
        $business_greedy_data = $this->prepareBusinessGreedyData();
        return BusinessTransformer::transform($business_greedy_data);
    }
    
    /**
     * Get before Standard Json API format for using in building array of Json objects
     * @return array
     */
    public function getBeforeStandardArray( ) {
        return BusinessTransformer::beforeStandard($this->prepareBusinessGreedyData());
    }
    
    /**
     * Get Standard Json Collection of all Businesses
     * @return array
     */
    public function getStandardJsonCollection() {
        $result["data"] = [];
        $instance = new static;
        foreach($instance->get() as $business_object){
            $result["data"][] = $business_object->getBeforeStandardArray();
        }//foreach($instance->get() as $business_object)
        return $result;
    }

}
