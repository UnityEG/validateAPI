<?php

namespace App\Http\Controllers;

use App\EssentialEntities\GeneralHelperTools;
use App\Http\Controllers\ApiController;
use App\Http\Models\Voucher;
use App\Http\Models\VoucherParameter;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class VouchersController extends ApiController {
    
//    todo Add JWTAuth middleware to apply on all methods in this controller

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $all_sold_vouchers = Voucher::orderBy( 'created_at', 'desc' )->get();
        return $this->respond( $all_sold_vouchers );
    }

    /**
     * Store a newly Voucher.
     *
     * @param  array $purchased_voucher_to_create
     * @return \App\Http\Models\Voucher
     */
    public function store( array $purchased_voucher_to_create ) {
//        todo if voucher parameter type is gift calculate specific fields is_gift, delivery_date and value else delivery_date will be today and value will equal value from voucher parameters
        $voucher_parameter_object = VoucherParameter::find($purchased_voucher_to_create['voucher_parameter_id']);
        $current_user_object = JWTAuth::parseToken()->authenticate();
        $purchased_voucher_to_create['user_id'] = $current_user_object->id;
        $purchased_voucher_to_create['is_gift'] = ($voucher_parameter_object->voucher_type == 'gift') ? TRUE : FALSE;
        $purchased_voucher_to_create['recipient_email'] = (!empty($purchased_voucher_to_create['recipient_email'])) ? $purchased_voucher_to_create['recipient_email'] : $current_user_object->email;
        $purchased_voucher_to_create['status'] = 'valid';
        ('gift' == $voucher_parameter_object->voucher_type) ?: $purchased_voucher_to_create['value'] = $voucher_parameter_object->value;
        $purchased_voucher_to_create['balance'] = $purchased_voucher_to_create['value'];
        $purchased_voucher_to_create['code'] = self::generateVoucherCode($voucher_parameter_object->voucher_type);
        
        // Convert local time to UTC time in order to save it in DB
        $purchased_voucher_to_create['delivery_date'] = ('gift' == $voucher_parameter_object->voucher_type) ? GeneralHelperTools::utcDateTime($purchased_voucher_to_create['delivery_date'] . ' 00:00:00', 'd/m/Y H:i:s') : Carbon::today();
//        expiry date -1 second
            $purchased_voucher_to_create['expiry_date'] = $voucher_parameter_object->valid_until->subSeconds(1);
        DB::beginTransaction();
        if($purchased_voucher = Voucher::create($purchased_voucher_to_create)){
            $voucher_parameter_update_data = [
                'is_purchased'       => TRUE,
                'purchased_quantity' => $voucher_parameter_object->purchased_quantity + 1
//                    todo calculate store_quantity if is_limited_quantity true
            ];
            (!$voucher_parameter_object->is_limited_quantity) ?  : $voucher_parameter_update_data['stock_quantity'] = $voucher_parameter_object->stock_quantity - 1;
            $voucher_parameter_object->update($voucher_parameter_update_data);
            DB::commit();
            return $purchased_voucher;
        }//if(Voucher::create($purchased_voucher_to_create))
        else{
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError( 'Internal Error');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show( $id ) {
        try {
            $voucher_object = Voucher::findOrFail( $id );
        } catch ( Exception $ex ) {
            return $this->respondNotFound( $ex->getMessage() );
        }

        return $this->respond( $voucher_object );
    }
    
//    Helper Methods
    
    public static function generateVoucherCode($voucher_param_type) {
        switch ( $voucher_param_type ) {
            case 'gift':
                $code = '3';
                break;
            case 'concession':
                $code = '4';
                break;
            case 'deal':
                $code = '5';
                break;
            case 'birthday':
                $code = '6';
                break;
            case 'discount':
                $code = '7';
                break;
        }//switch ( $voucher_param_type )
        $code .= mt_rand(00000001, 99999999); // better than rand()
        // call the same function if the barcode exists already
        if (Voucher::where('code', '=', $code)->exists()) {
            return self::generateVoucherCode($voucher_param_type);
        }//if (Voucher::where('code', '=', $code)->exists())
        if (strlen($code) < 9) {
            return self::generateVoucherCode($voucher_param_type);
        }
        // otherwise, it's valid and can be used
        return $code;
    }
}
