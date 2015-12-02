<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherImage extends Model {

    protected $table = 'voucher_images';
    protected $fillable = [
        'name',
        'extension',
        'voucher_type'
    ];

    /**
     * Relationship between VoucherImage Model and VoucherParameter Model (One to Many)
     * @return object
     */
    public function voucherParameter() {
        return $this->hasMany( 'App\Http\Models\VoucherParameter', 'voucher_image_id', 'id' );
    }

//    todo create getTransformedArray method in VoucherImage Model
//    todo create getTransformedCollection method in VoucherImage Model
//    todo create getBeforeStandard method in VoucherImage Model
//    todo create prepareVoucherImgaeGreedyData method in VoucherImage Model
}
