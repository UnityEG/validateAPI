<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class GiftVoucher extends Model {

    //
    protected $table = 'giftvoucher';
    protected $guarded = ['id'];
    //protected $fillable = ['title', 'body'];

    public function customer() {
        return $this->belongsTo('Customer');
    }

    public function parameter() {
        return $this->belongsTo('App\Http\Models\GiftVoucherParameter', 'gift_vouchers_parameters_id');
    }
    
}
