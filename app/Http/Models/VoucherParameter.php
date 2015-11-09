<?php

namespace App\Http\Models;

use BusinessTransformer;
use UserTransformer;
use UseTermTransformer;
use VoucherImageTransformer;
use VoucherParametersTransformer;
use Illuminate\Database\Eloquent\Model;

class VoucherParameter extends Model {
    protected $table = "voucher_parameters";
    protected $dates = ['purchase_start', 'purchase_expiry', 'valid_from', 'valid_until'];
    protected $fillable = [
        'business_id',
        'user_id',
        'voucher_image_id',
        'voucher_type',
        'title',
        'purchase_start',
        'purchase_expiry',
        'is_expire',
        'is_display',
        'is_purchased',
        'valid_from',
        'valid_for_amount',
        'valid_for_units',
        'valid_until',
        'is_limited_quantity',
        'quantity',
        'purchased_quantity',
        'stock_quantity',
        'short_description',
        'long_description',
        'is_single_use',
        'no_of_uses',
        'retail_value',
        'value',
        'min_value',
        'max_value',
        'is_valid_during_month',
        'discount_percentage',
    ];

    /**
     * Relationship between VoucherParameter Model and Business Model (many to one)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function business() {
        return $this->belongsTo( 'App\Http\Models\Business', 'business_id', 'id' );
    }
    
    /**
     * Relationship between VoucherParameter Model and User Model (Many to One)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user( ) {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
    /**
     * Relationship between VoucherParameter Model and VoucherImage Model (Many to One)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function voucherImage() {
        return $this->belongsTo( 'App\Http\Models\VoucherImage', 'voucher_image_id', 'id' );
    }

    /**
     * Relationship method between Voucher Model and UseTerm Model (many to many)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function useTerms() {
        return $this->belongsToMany( 'App\Http\Models\UseTerm', 'voucher_parameters_use_terms_rel', 'voucher_parameter_id', 'use_term_id' );
    }
    
    /**
     * Relationship between VoucherParameter Model and Voucher Model ( one to many)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vouchers( ) {
        return $this->hasMany('App\Http\Models\Voucher', 'voucher_parameter_id', 'id');
    }
    
    /**
     * Get Standard Json collection of Voucher Parameter objects
     * @return array
     */
    public static function getStandardJsonCollection(){
        $instance = new static;
        $response["data"] = [];
        foreach ( $instance->get() as $voucher_parameter_object) {
            $response["data"][] = $voucher_parameter_object->getBeforeStandardArray();
        }
        return $response;
    }
    
    /**
     * Get Standard Json API format for single object
     * @return array
     */
    public function getStandardJsonFormat( ) {
        return VoucherParametersTransformer::transform( $this->prepareVoucherParameterGreedyData());
    }
    
    /**
     * Get Before standard Json API format for single object
     * @return array
     */
    public function getBeforeStandardArray( ) {
        return VoucherParametersTransformer::beforeStandard( $this->prepareVoucherParameterGreedyData());
    }
    
    /**
     * Prepare Data of VoucherParameter obect and its relationships data in a greedy way to be used in generating Standard Json API format
     * @return array
     */
    private function prepareVoucherParameterGreedyData() {
//        todo use Facade instead of instantiating objects inside prepareVoucherParameterGreedyData method
        $voucher_parameters_greedy_array = $this->load(['business', 'user', 'voucherImage', 'useTerms'])->toArray();
        (empty($voucher_parameters_greedy_array['business'])) ? : $voucher_parameters_greedy_array['business'] = BusinessTransformer::transform( $voucher_parameters_greedy_array['business']);
        (empty($voucher_parameters_greedy_array['user'])) ?  : $voucher_parameters_greedy_array['user'] = UserTransformer::transform( $voucher_parameters_greedy_array['user']);
        (empty($voucher_parameters_greedy_array['voucher_image'])) ?  : $voucher_parameters_greedy_array['voucher_image'] = VoucherImageTransformer::transform( $voucher_parameters_greedy_array['voucher_image']);
        (empty($voucher_parameters_greedy_array['use_terms'])) ?  : $voucher_parameters_greedy_array['use_terms'] = UseTermTransformer::transformCollection( $voucher_parameters_greedy_array['use_terms']);
        return $voucher_parameters_greedy_array;
    }

}
