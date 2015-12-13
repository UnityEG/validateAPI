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
        return $this->hasMany('App\Http\Models\VoucherValidationLog', 'business_id', 'id');
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
    
    /**
     * Get Active logo object for the business
     * @return BusinessLogo
     */
    public function getActiveLogo( ) {
        return $this->businessLogos()->where('id', $this->logo_id)->first();
    }
    
    /**
     * Get Standard Json API Format for single object
     * @return array
     */
    public function getStandardJsonFormat( ) {
        return BusinessTransformer::transform($this->prepareBusinessGreedyData());
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
    
    /**
     * Create New Business
     * @param array $raw_data
     * @return boolean | array
     */
    public function createNewBusiness(array $raw_data){
        $modified_data = $this->commonStoreUpdate($raw_data);
        $current_user_id = (int)\JWTAuth::parseToken()->authenticate()->id;
        $modified_data[ 'is_new' ]            = 1;
        $modified_data[ 'is_active' ]         = 0;
        $modified_data[ 'is_featured' ]       = 0;
        $modified_data[ 'is_display' ]        = 1;
        $modified_data['code'] = $this->generateBusinessCode();
        $modified_data['created_by'] = $current_user_id;
        \DB::beginTransaction();
        $created_business = $this->create($modified_data);
        if ( is_object( $created_business ) ) {
            (empty($modified_data['business_type_ids'])) ?  : $created_business->businessTypes()->attach($modified_data['business_type_ids']);
            $created_business->users()->attach([$current_user_id]);
            \DB::commit();
            return $created_business->getStandardJsonFormat();
        }else{
            \DB::rollBack();
            return FALSE;
        }//if ( is_object( $created_business ) )
    }
    
    /**
     * Update existing Business
     * @param array $raw_data
     * @return boolean | array
     */
    public function updateBusiness(array $raw_data){
        $modified_data = $this->commonStoreUpdate($raw_data);
        \DB::beginTransaction();
        if ( $this->update( $modified_data) ) {
            (empty($modified_data['business_type_ids'])) ?  : $this->businessTypes()->sync( $modified_data['business_type_ids']);
//            todo update relationships between business and users
//            todo update relationships between users and user groups according to updated business types
            \DB::commit();
            return $this->getStandardJsonFormat();
        }else{
            \DB::rollBack();
            return FALSE;
        }//if($this->save( $modified_data))
    }
    
    /**
     * Prepare Business Data with all its relationships data in a greedy way in standard transform
     * @return array
     */
    private function prepareBusinessGreedyData( ) {
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
     * Prepare Data for storing and updating business
     * @param array $raw_data
     * @return array 
     */
    private function commonStoreUpdate(array $raw_data){
        (!$logo_id = array_deep_search( $raw_data, 'logo_id')) ?  : $modified_data['logo_id'] = (int)$logo_id;
        (!$city_id = array_deep_search( $raw_data, 'city_id')) ?  : $modified_data['city_id'] = (int)$city_id;
        (!$region_id = array_deep_search( $raw_data, 'region_id')) ?  : $modified_data['region_id'] = (int)$region_id;
        (!$town_id = array_deep_search( $raw_data, 'town_id')) ?  : $modified_data['town_id'] = (int)$town_id;
        (!$postcode_id = array_deep_search( $raw_data, 'postcode_id')) ?  : $modified_data['postcode_id'] = (int)$postcode_id;
        (!$industry_id = array_deep_search( $raw_data, 'industry_id')) ?  : $modified_data['industry_id'] = (int)$industry_id;
        (!$business_type_ids = array_deep_search( $raw_data, 'business_type_ids')) ?  : $modified_data['business_type_ids'] = array_map( 'intval', $business_type_ids);
        $is_active = array_deep_search($raw_data, 'is_active');
        if ( $is_active ) {
            $modified_data['is_active'] = ("false" !== $is_active) ? TRUE : FALSE;
        }//if ( $is_active )
        (!$business_name = array_deep_search( $raw_data, 'business_name')) ?  : $modified_data['business_name'] = (string)$business_name;
        (!$trading_name = array_deep_search( $raw_data, 'trading_name')) ?  : $modified_data['trading_name'] = (string)$trading_name;
        (!$address1 = array_deep_search( $raw_data, 'address1')) ?  : $modified_data['address1'] = (string)$address1;
        (!$address2 = array_deep_search( $raw_data, 'address2')) ?  : $modified_data['address2'] = (string)$address2;
        (!$phone = array_deep_search( $raw_data, 'phone')) ?  : $modified_data['phone'] = (string)$phone;
        (!$website = array_deep_search( $raw_data, 'website')) ?  : $modified_data['website'] = (string)$website;
        (!$business_email = array_deep_search( $raw_data, 'business_email')) ?  : $modified_data['business_email'] = (string)$business_email;
        (!$contact_name = array_deep_search( $raw_data, 'contact_name')) ?  : $modified_data['contact_name'] = (string)$contact_name;
        (!$contact_mobile = array_deep_search( $raw_data, 'contact_mobile')) ?  : $modified_data['contact_mobile'] = (string)$contact_mobile;
        $is_featured = array_deep_search($raw_data, 'is_featured');
        if ( $is_featured ) {
            $modified_data['is_featured'] = ("false" !== $is_featured) ? TRUE : FALSE;
        }//if ( $is_featured )
        $is_display = array_deep_search($raw_data, 'is_display');
        if ( $is_display ) {
            $modified_data['is_display'] = ("false" !== $is_display) ? TRUE : FALSE;
        }//if ( $is_display )
        (!$available_hours_mon = array_deep_search( $raw_data, 'available_hours_mon')) ? : $modified_data['available_hours_mon'] = (string)$available_hours_mon;
        (!$available_hours_tue = array_deep_search( $raw_data, 'available_hours_tue')) ? : $modified_data['available_hours_tue'] = (string)$available_hours_tue;
        (!$available_hours_wed = array_deep_search( $raw_data, 'available_hours_wed')) ? : $modified_data['available_hours_wed'] = (string)$available_hours_wed;
        (!$available_hours_thu = array_deep_search( $raw_data, 'available_hours_thu')) ? : $modified_data['available_hours_thu'] = (string)$available_hours_thu;
        (!$available_hours_fri = array_deep_search( $raw_data, 'available_hours_fri')) ? : $modified_data['available_hours_fri'] = (string)$available_hours_fri;
        (!$available_hours_sat = array_deep_search( $raw_data, 'available_hours_sat')) ? : $modified_data['available_hours_sat'] = (string)$available_hours_sat;
        (!$available_hours_sun = array_deep_search( $raw_data, 'available_hours_sun')) ? : $modified_data['available_hours_sun'] = (string)$available_hours_sun;
        return $modified_data;
    }
    
    /**
     * Generate internation business code for POS
     * @return integer
     */
    private function generateBusinessCode() {
        $code = mt_rand(10000000, 99999999);
        return ((8 > strlen( $code)) || $this->where('code', $code)->exists()) ? $this->generateBusinessCode() : $code;
    }

}
