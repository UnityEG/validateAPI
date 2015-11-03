<?php

namespace App\Http\Controllers\VouchersControllers;

use App\EssentialEntities\Transformers\VoucherValidationLogTransformer;
use App\Http\Controllers\ApiController;
use App\Http\Models\Voucher;
use App\Http\Models\VoucherValidationLog;
use App\Http\Requests\Vouchers\VoucherValidationRequests\ValidateVoucherRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class VoucherValidationLogsController extends ApiController
{
//    todo remove unused methods in VoucherValidationLogsController class
    
//    todo remove voucherValidationLogTransformer property
    private $voucherValidationLogTransformer;
    
    public function __construct(VoucherValidationLogTransformer $voucher_validation_log_transformer) {
//        todo remove VoucherValidationLogTransformer dependency
//        todo apply JWTAuth middleware on all methods in this controller
        $this->voucherValidationLogTransformer = $voucher_validation_log_transformer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }
    
    /**
     * Get all validation logs for specific voucher
     * @param integer $voucher_id
     * @return Json response
     */
    public function getAllLogs( $voucher_id) {
//        todo use getStandardJsonFromat method in VoucherValidationLog Model to respond with standard Json format
        try{
            $voucher_object = Voucher::findOrFail((int)$voucher_id);
            $voucher_validation_logs = $voucher_object->voucherValidationLogs->toArray();
        } catch (Exception $ex) {
            return $this->respondNotFound($ex->getMessage());
        }
        
        $voucher_validation_logs_json = $this->voucherValidationLogTransformer->transformCollection( $voucher_validation_logs);
        
        return $this->respond($voucher_validation_logs_json);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function validateVoucher(  ValidateVoucherRequest $request)
    {
//        todo use arrayKeySearchRecursivly method in GeneralHelperTools class
        $voucher_validation_log_data['voucher_id'] = (int)$request->get('data')['relations']['voucher']['data']['voucher_id'];
        $voucher_validation_log_data['business_id'] = (int)$request->get('data')['relations']['business']['data']['business_id'];
        $voucher_validation_log_data['value'] = ($request->get('data')['value']);
        $voucher_validation_log_data['user_id'] = JWTAuth::parseToken()->authenticate()->id;
//        get voucher object related to the current validation process
        $voucher_object = Voucher::find($voucher_validation_log_data['voucher_id']);
        $new_balance = $voucher_object->balance - $voucher_validation_log_data['value'];
        $voucher_validation_log_data['balance'] = $new_balance;
//        update voucher status voucher will be validated if it's balance less than 1 $ or single use
//        todo use trn instead of regular if statment
//        todo remove $new_status and use $voucher_validation_log_data['log'] directly to save some memory and processing
        if ( (1 ===  (int)$voucher_object->voucherParameter->no_of_uses) || (1 >= $new_balance)) {
            $new_status = 'validated';
        }else{
            $new_status = 'valid';
        }
        $new_validation_times = (int)$voucher_object->validation_times++;
        $voucher_validation_log_data['log'] = $new_status;
        DB::beginTransaction();
        if ( $voucher_validation_log_object = VoucherValidationLog::create($voucher_validation_log_data) ) {
//update voucher information (status, balance, validation_times, last_validation_date)
            $update_voucher_data = [
                'status' => $new_status,
                'balance' => $new_balance,
                'validation_times' => $new_validation_times,
                'last_validation_date' => $voucher_validation_log_object->created_at
            ];
            $voucher_object->update($update_voucher_data);
            DB::commit();
//            todo use getStandardJsonFormat method in VoucherValidationLog Model to respond with standard Json format
            return $this->respond($voucher_validation_log_object);
        }//if ( $voucher_validation_log_object = VoucherValidationLog::create($voucher_validation_log_data) )
        else{
            DB::rollBack();
            return $this->respondInternalError();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
