<?php

namespace app\Http\Models;

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
        'quantity',
        'purchased_quantity',
        'stock_quantity',
        'short_description',
        'long_description',
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
     * @return object
     */
    public function business() {
        return $this->belongsTo( 'App\Http\Models\Business', 'business_id', 'id' );
    }
    
    /**
     * Relationship between VoucherParameter Model and User Model (Many to One)
     * @return object
     */
    public function user( ) {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
    /**
     * Relationship between VoucherParameter Model and VoucherImage Model (Many to One)
     * @return objec
     */
    public function voucherImage() {
        return $this->belongsTo( 'App\Http\Models\VoucherImage', 'voucher_image_id', 'id' );
    }

    /**
     * Relationship method between Voucher Model and UseTerm Model (many to many)
     * @return object
     */
    public function useTerms() {
        return $this->belongsToMany( 'App\Http\Models\UseTerm', 'voucher_parameters_use_terms_rel', 'voucher_parameter_id', 'use_term_id' );
    }
    
    /**
     * Relationship between VoucherParameter Model and Voucher Model ( one to many)
     * @return object
     */
    public function vouchers( ) {
        return $this->hasMany('App\Http\Models\Voucher', 'voucher_parameter_id', 'id');
    }

}
