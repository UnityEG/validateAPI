<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model {
    
    protected $table = 'business';
    protected $dates = ['deleted_at'];
    //protected $fillable = ['title', 'body'];
    
    /**
     * Relationship between Business Model and User Model (many to one)
     * @return object
     */
    public function User() {
        return $this->belongsTo('App\User');
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
    
    
//    Helpers
    
    /**
     * Get Active logo object for the business
     * @return object
     */
    public function getActiveLogo( ) {
        return $this->businessLogos()->where('id', $this->logo_id)->first();
    }

}
