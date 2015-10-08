<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use App\aaa\Transformers\BusinessTransformer;
use App\aaa\Transformers\CityTransformer;
use App\aaa\Transformers\RegionTransformer;
use App\aaa\Transformers\TownTransformer;
use App\aaa\Transformers\PostcodeTransformer;
use App\aaa\Transformers\UserGroupTransformer;
use App\aaa\Transformers\UserTransformer;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable,
        CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city_id',
        'region_id',
        'town_id',
        'postcode_id',
        'facebook_user_id',
        'is_active',
        'email', 
        'password',
        'title',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'address1',
        'address2',
        'phone',
        'mobile',
        'is_notify_deal'
        ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function Merchant() {
        return $this->hasOne('App\Merchant');
    }

    public function FeedBack() {
        return $this->hasMany('App\FeedBack');
    }
    
    /**
     * Relationship between User Model and VoucherParameter Model (One to Many)
     * @return object
     */
    public function voucherParameters( ) {
        return $this->hasMany('App\Http\Models\VoucherParameter', 'user_id', 'id');
    }
    
    /**
     * Relationship between User Model and Voucher Model ( one to many)
     * @return object
     */
    public function vouchers( ) {
        return $this->hasMany('App\Http\Models\Voucher', 'user_id', 'id');
    }

    /**
     * Relationship between User Model and VoucherValidationLog Model ( one to many)
     * @return object
     */
    public function voucherValidationLogs( ) {
        return $this->hasMany('App\Http\Models\VoucherValidationLog', 'user_id', 'id');
    }
    
    /**
     * Relationship between User Model and UserGroup Model (many to many)
     * @return object
     */
    public function userGroups( ) {
        return $this->belongsToMany('App\Http\Models\UserGroup', 'users_user_groups_rel', 'user_id', 'user_group_id');
    }
    
    /**
     * Relationship between User Model and City Model (many to one)
     * @return object
     */
    public function city( ) {
        return $this->belongsTo('App\Http\Models\City', 'city_id', 'id');
    }
    
    /**
     * Relationship between User Model and Region Model (many to one)
     * @return object
     */
    public function region( ) {
        return $this->belongsTo('App\Http\Models\Region', 'region_id', 'id');
    }
    
    /**
     * Relationship between User Model and Town Model (many to one)
     * @return object
     */
    public function town( ) {
        return $this->belongsTo('App\Http\Models\Town', 'town_id', 'id');
    }
    
    /**
     * Relationship between User Model and Postcode Model (many to one)
     * @return object
     */
    public function postcode( ) {
        return $this->belongsTo('App\Http\Models\Postcode', 'postcode_id', 'id');
    }
  
    /**
     * Relationship between User Model and Business Model (many to many)
     * @return object
     */
    public function business( ) {
        return $this->belongsToMany('App\Http\Models\Business', 'users_business_rel', 'user_id', 'business_id');
    }
    
    
//    Helpers
    
    /**
     * Get Formal user full name
     * @return string
     */
    public function getName( ) {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }
    
    /**
     * check if user has the rule or not
     * @param string $rule_name
     * @return boolean
     */
    public function hasRule( $rule_name) {
//        todo modify response
        $user_groups = $this->userGroups;
        foreach ( $user_groups as $user_group) {
            if(is_object($user_group->rules()->where('name', $rule_name)->first())){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * Get Standard Json API format for singel object
     * @return array
     */
    public function getStandardJsonTransform( ) {
        return (new UserTransformer())->transform($this->prepareUserGreedyData());
    }
    
    /**
     * Get Before Standard Json API format for using in building array of Json objects
     * @return array
     */
    public function getBeforeStandardArray( ) {
        return (new UserTransformer())->beforeStandard($this->prepareUserGreedyData());
    }
    
    /**
     * Prepare User data with all its relationships data in greedy way with the standard transform
     * @return array
     */
    private function prepareUserGreedyData() {
        $user_greedy_array = $this->load('city', 'region', 'town', 'postcode', 'userGroups', 'business')->toArray();
        (empty($user_greedy_array['city'])) ?  : (new CityTransformer())->transform($user_greedy_array['city']);
        (empty($user_greedy_array['region'])) ?  : (new RegionTransformer())->transform( $user_greedy_array['region']);
        (empty($user_greedy_array['town'])) ?  : (new TownTransformer())->transform( $user_greedy_array['town']);
        (empty($user_greedy_array['postcode'])) ?  : (new PostcodeTransformer())->transform( $user_greedy_array['postcode']);
        (empty($user_greedy_array['user_groups'])) ?  : (new UserGroupTransformer())->transformCollection( $user_greedy_array['user_groups']);
        (empty($user_greedy_array['business'])) ?  : (new BusinessTransformer())->transformCollection( $user_greedy_array['business']);
        return $user_greedy_array;
    }

}
