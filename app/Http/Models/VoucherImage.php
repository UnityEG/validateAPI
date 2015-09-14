<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherImage extends Model
{
    protected $table = 'voucher_images';
    protected $guarded = ['id'];
    
    /**
     * Relationship between VoucherImage Model and VoucherParameter Model (One to Many)
     * @return object
     */
    public function voucherParameter( ) {
        return $this->hasMany('App\Http\Models\VoucherParameter', 'voucher_image_id', 'id');
    }
}
