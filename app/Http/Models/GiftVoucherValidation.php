<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class GiftVoucherValidation extends Model {

    //
    protected $table = 'giftvouchervalidation';
    protected $guarded = ['id'];

    //protected $fillable = ['title', 'body'];

    public function voucher() {
        return $this->belongsTo('App\Http\Models\GiftVoucher', 'giftvoucher_id');
    }

}
