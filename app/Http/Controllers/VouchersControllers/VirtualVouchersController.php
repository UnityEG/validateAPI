<?php

namespace App\Http\Controllers\VouchersControllers;

use App\Http\Controllers\ApiController;
use App\Http\Models\VirtualVoucher;
use App\Http\Requests;
use Illuminate\Http\Request;


class VirtualVouchersController extends ApiController
{
    
    /**
     * Instance of VirtualVoucher Model
     * @var VirtualVoucher
     */
    private $VirtualVoucherModel;

    public function __construct( VirtualVoucher $virtual_voucher_model) {
        $this->VirtualVoucherModel = $virtual_voucher_model;
    }
    
    /**
     * Show Virtual Voucher Image
     * @param integer $code
     * @return Response
     */
    public function showVirtualVoucherImage($code){
        $voucher_image = $this->VirtualVoucherModel->find( $code );
        return ($voucher_image) ? \Response::download( $voucher_image, null, [], null) : $this->respondNotFound();
    }
}
