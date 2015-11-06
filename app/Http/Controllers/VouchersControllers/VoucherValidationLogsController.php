<?php

namespace App\Http\Controllers\VouchersControllers;

use GeneralHelperTools;
use App\Http\Controllers\ApiController;
use App\Http\Models\Voucher;
use App\Http\Models\VoucherValidationLog;
use App\Http\Requests\Vouchers\VoucherValidationRequests\ValidateVoucherRequest;
use DB;
use JWTAuth;

class VoucherValidationLogsController extends ApiController
{
    
    public function __construct() {
        $this->middleware('jwt.auth');
    }
    
    /**
     * Get all validation logs for specific voucher
     * @param integer $voucher_id
     * @return array
     */
    public function getAllLogs( $voucher_id) {
        $response["data"] = [];
        foreach ( VoucherValidationLog::where('voucher_id', (int)$voucher_id)->get() as $voucher_validation_log_object) {
            $response["data"][] = $voucher_validation_log_object->getBeforeStandardArray();
        }
        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Vouchers\VoucherValidationRequests\ValidateVoucherRequest  $request
     * @return array
     */
    public function validateVoucher(  ValidateVoucherRequest $request)
    {
        $voucher_validation_log_data['voucher_id'] = (int)GeneralHelperTools::arrayKeySearchRecursively( $request->get( 'data'), 'voucher_id');
        $voucher_validation_log_data['business_id'] = (int)GeneralHelperTools::arrayKeySearchRecursively( $request->get( 'data'), 'business_id');
        $voucher_validation_log_data['value'] = (double)GeneralHelperTools::arrayKeySearchRecursively( $request->get( 'data'), 'value');
        $voucher_validation_log_data['user_id'] = JWTAuth::parseToken()->authenticate()->id;
        $voucher_object = Voucher::find($voucher_validation_log_data['voucher_id']);
        $voucher_object->balance = $voucher_validation_log_data['balance'] = $voucher_object->balance - $voucher_validation_log_data['value'];
        if ( (1 ===  (int)$voucher_object->voucherParameter->no_of_uses) || (1 >= $voucher_validation_log_data['balance'])) {
            $voucher_object->status = $voucher_validation_log_data['log'] = 'validated';
        }else{
            $voucher_object->status = $voucher_validation_log_data['log'] = 'valid';
        }//if((1===(int)$voucher_object->voucherParameter->no_of_uses)||(1>=$voucher_validation_log_data['balance']))
        $voucher_object->validation_times = (int)$voucher_object->validation_times + 1;
        DB::beginTransaction();
        $voucher_validation_log_object = VoucherValidationLog::create($voucher_validation_log_data);
        if ( is_object( $voucher_validation_log_object ) ) {
            $voucher_object->last_validation_date = $voucher_validation_log_object->created_at;
            if ( $voucher_object->save() ) {
                DB::commit();
                return $voucher_validation_log_object->getStandardJsonFormat();
            }else{
                DB::rollBack();
                return $this->respondInternalError();
            }//if ( $voucher_object->save() )
        }//if ( $voucher_validation_log_object = VoucherValidationLog::create($voucher_validation_log_data) )
        else{
            DB::rollBack();
            return $this->respondInternalError();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $voucher_validation_log_id
     * @return array
     */
    public function show($voucher_validation_log_id)
    {
        return VoucherValidationLog::findOrFail((int)$voucher_validation_log_id)->getStandardJsonFormat();
    }
}
