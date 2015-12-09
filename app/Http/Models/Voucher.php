<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use VoucherTransformer;
use VoucherParametersTransformer;
use OrderTransformer;
use UserTransformer;
use VoucherValidationLogTransformer;

class Voucher extends Model
{
//    todo modify @return documentation to be the correct type for all relationship methods
    protected $table = "vouchers";
    protected $dates = ['delivery_date', 'expiry_date', 'last_validation_date'];
    protected $fillable = [
        'user_id',
        'voucher_parameter_id',
        'order_id',
        'status',
        'code',
        'value',
        'balance',
        'is_mail_sent',
        'is_instore',
        'delivery_date',
        'recipient_email',
        'message',
        'expiry_date',
        'validation_times',
        'last_validation_date',
    ];
    
    /**
     * Relationship between Voucher Model and User Model ( many to one)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user( ) {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
    /**
     * Relationship between Voucher Model and VoucherParameter Model ( many to one)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function voucherParameter( ) {
        return $this->belongsTo('App\Http\Models\VoucherParameter', 'voucher_parameter_id', 'id');
    }
    
    /**
     * Relationship between Voucher Model and VoucherValidationLog Model (one to many)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function voucherValidationLogs( ) {
        return $this->hasMany('App\Http\Models\VoucherValidationLog', 'voucher_id', 'id');
    }
    
    /**
     * Relationship between Voucher Model and Order Model (many to one)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(){
        return $this->belongsTo('App\Http\Models\Order', 'order_id', 'id');
    }
    
    /**
     * Get Standard Json Collection for all Voucher objects
     * @return array
     */
    public static function getStandardJsonCollection(){
        $instance = new static;
        $response["data"] = [];
        foreach ( $instance->get() as $voucher_object ) {
            $response["data"][] = $voucher_object->getBeforeStandardArray();
        }//foreach ( $instance->get() as $voucher_object )
        return $response;
    }
    
    /**
     * Get Array to be converted to Standard Json Format for single object
     * @return array
     */
    public function getStandardJsonFormat( ) {
        return VoucherTransformer::transform($this->prepareVoucherGreedyData());
    }
    
    /**
     * Get Before Standard array to get Json collection object
     * @return array
     */
    public function getBeforeStandardArray(){
        return VoucherTransformer::beforeStandard($this->prepareVoucherGreedyData());
    }
    
    /**
     * get Virtual Voucher Data
     * @return array
     */
    public function getVirtualVoucherData( ) {
        $voucher_parameter_object = $this->voucherParameter;
        $business_object = $voucher_parameter_object->business;
        $customer_object = $this->user;
        $business_logo_object = $business_object->getActiveLogo();
        $business_logo_filename = (is_object($business_logo_object)) ? config( 'validateconf.default_business_logos_path') . $business_logo_object->name . '.png' : 'voucher/images/voucher_m_logo.png';
        // get Gift Vouchers Parameter Terms Of Use
        $terms_of_use_objects = $voucher_parameter_object->useTerms()->get(['name'])->toArray();
        $terms_of_use = implode(' ● ', array_pluck($terms_of_use_objects, 'name'));
        //
        return[
            'm_logo_filename' => $business_logo_filename,
            'qr_code' => $this->code,
            'delivery_date' => $this->delivery_date,
            'expiry_date' => $this->expiry_date,
            'voucher_value' => $this->value,
            'merchant_business_name' => $business_object->business_name,
            'voucher_title' => $voucher_parameter_object->title,
            'TermsOfUse' => $terms_of_use,
            'merchant_business_address1' => $business_object->address1,
            'business_suburb' => $business_object->postcode->suburb,
            'merchant_business_phone' => $business_object->phone,
            'merchant_business_website' => $business_object->website,
            'recipient_email' => $this->recipient_email,
            'customer_name' => $customer_object->getName(),
            'customer_email' => $customer_object->email,
        ];
    }
    
    /**
     * Prepare Data to be used in Json response
     * @return array
     */
    private function prepareVoucherGreedyData() {
        $voucher_greedy_array = $this->load(['voucherParameter', 'order', 'user', 'voucherValidationLogs'])->toArray();
        (empty($voucher_greedy_array['voucher_parameter'])) ?  : $voucher_greedy_array['voucher_parameter'] = VoucherParametersTransformer::transform($voucher_greedy_array['voucher_parameter']);
        (empty($voucher_greedy_array['order'])) ?  : $voucher_greedy_array['order'] = OrderTransformer::transform($voucher_greedy_array['order']);
        (empty($voucher_greedy_array['user'])) ?  : $voucher_greedy_array['user'] = UserTransformer::transform($voucher_greedy_array['user']);
        (empty($voucher_greedy_array['voucher_validation_logs'])) ?  : $voucher_greedy_array['voucher_validation_logs'] = VoucherValidationLogTransformer::transformCollection($voucher_greedy_array['voucher_validation_logs']);
//        add merchant name
//        $voucher_greedy_array['merchant_name'] = $this->voucherParameter->business->business_name;
        return $voucher_greedy_array;
    }
}
