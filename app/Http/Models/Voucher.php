<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = "vouchers";
    protected $dates = ['delivery_date', 'expiry_date', 'last_validation_date'];
    protected $fillable = [
        'user_id',
        'voucher_parameter_id',
        'status',
        'code',
        'value',
        'balance',
        'is_gift',
        'delivery_date',
        'recipient_email',
        'message',
        'expiry_date',
        'validation_times',
        'last_validation_date',
    ];
    
    /**
     * Relationship between Voucher Model and User Model ( many to one)
     * @return object
     */
    public function user( ) {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
    /**
     * Relationship between Voucher Model and VoucherParameter Model ( many to one)
     * @return object
     */
    public function voucherParameter( ) {
        return $this->belongsTo('App\Http\Models\VoucherParameter', 'voucher_parameter_id', 'id');
    }
    
    /**
     * Relationship between Voucher Model and VoucherValidationLog Model (one to many)
     * @return object
     */
    public function voucherValidationLogs( ) {
        return $this->hasMany('App\Http\Models\VoucherValidationLog', 'voucher_id', 'id');
    }
}
