<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class GiftVoucherParameter extends Model {

    //
    protected $table = 'gift_vouchers_parameters';
    protected $guarded = ['id'];

    //protected $fillable = ['title', 'body'];

    public function Merchant() {
        return $this->belongsTo('App\Http\Models\Merchant', 'MerchantID');
    }
    
    public function GiftVoucher() {
        return $this->hasMany('App\Http\Models\GiftVoucher', 'gift_vouchers_parameters_id');
    }

}
