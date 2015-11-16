<?php

namespace App\Http\Controllers\VouchersControllers;

use App\Http\Controllers\ApiController;
use App\Http\Models\Voucher;
use App\Http\Models\VoucherValidationLog;
use App\Http\Requests\Vouchers\VoucherValidationRequests\CheckVoucherRequest;
use App\Http\Requests\Vouchers\VoucherValidationRequests\ValidateVoucherRequest;
use DB;
use GeneralHelperTools;
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
     * @param  ValidateVoucherRequest  $request
     * @return array
     */
    public function validateVoucher(  ValidateVoucherRequest $request)
    {
        $voucher_validation_log_data['voucher_id'] = (int)GeneralHelperTools::arrayKeySearchRecursively( $request->get( 'data'), 'voucher_id');
        $voucher_validation_log_data['business_id'] = (int)GeneralHelperTools::arrayKeySearchRecursively( $request->get( 'data'), 'business_id');
        $voucher_validation_log_data['value'] = (double)GeneralHelperTools::arrayKeySearchRecursively( $request->get( 'data'), 'value');
        $voucher_validation_log_data['user_id'] = JWTAuth::parseToken()->authenticate()->id;
        $voucher_object = Voucher::find($voucher_validation_log_data['voucher_id']);
        $voucher_parameter_object = $voucher_object->voucherParameter;
        $voucher_object->balance = $voucher_validation_log_data['balance'] = $voucher_object->balance - $voucher_validation_log_data['value'];
        $voucher_object->validation_times = (int)$voucher_object->validation_times + 1;
        $is_validated = (bool)( ($voucher_parameter_object->is_single_use) || (1 >= $voucher_object->balance) || ($voucher_parameter_object->no_of_uses <= $voucher_object->validation_times) );
        if ( $is_validated ) {
            $voucher_object->status = $voucher_validation_log_data['log'] = 'validated';
        }else{
            $voucher_object->status = $voucher_validation_log_data['log'] = 'valid';
        }//if ( $is_validated )
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
    
    /**
     * Check Validation of Purchased Voucher
     * @param CheckVoucherRequest $request
     * @param Voucher $voucher_model
     * @return array
     */
    public function checkVoucher( CheckVoucherRequest $request, Voucher $voucher_model){
        return $voucher_model->where('code', $request->get('data[voucher_code]', '', TRUE))->first()->getStandardJsonFormat();
    }
}
