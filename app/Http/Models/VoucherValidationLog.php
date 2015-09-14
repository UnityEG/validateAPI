<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherValidationLog extends Model
{
    protected $table = 'voucher_validation_log';
    
    protected $dates = ['date'];
    
    protected $guarded = ['id'];
    
    protected $fillable = [
        'voucher_id',
        'business_id',
        'user_id',
        'date',
        'value',
        'balance',
        'log'
    ];
    
    /**
     * Relationship between VoucherValidationLog Model and Voucher Model (many to one)
     * @return object
     */
    public function voucher( ) {
        return $this->belongsTo('App\Http\Models\Voucher', 'voucher_id', 'id');
    }
    
    /**
     * Relationship between VoucherValidationLog Model and Business Model (many to one)
     * @return object
     */
    public function business( ) {
        return $this->belongsTo('App\Http\Models\Business', 'business_id', 'id');
    }
    
    /**
     * Relationship between VoucherValidationLog Model and User Model (many to one)
     * @return object
     */
    public function user( ) {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
