<?php

namespace App\Http\Controllers\VouchersControllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class VirtualVouchersController extends Controller
{
    public function showVirtualVoucherImage($code){
        $path = "VirtualVouchers/".md5($code).".png";
        return (file_exists( public_path($path))) ? asset( $path ) : 'Invalid code or not found Image';
    }
}
