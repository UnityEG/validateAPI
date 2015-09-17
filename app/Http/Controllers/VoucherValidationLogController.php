<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Models\Voucher;
use App\Http\Models\VoucherValidationLog;
use App\Http\Requests\ValidateVoucherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VoucherValidationLogController extends ApiController
{
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
        try{
            $voucher_object = Voucher::findOrFail((int)$voucher_id);
            $voucher_validation_logs = $voucher_object->voucherValidationLogs;
        } catch (\Exception $ex) {
            return $this->respondNotFound($ex->getMessage());
        }
//        todo building VoucherValidationLogTransformer to build formal Json respone
        return $voucher_validation_logs;
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
        $voucher_validation_log_data['voucher_id'] = (int)$request->get('data')['relations']['voucher']['voucher_id'];
        $voucher_validation_log_data['value'] = ($request->get('data')['value']);
//        todo continue preparing data ( 'user_id')
//        get voucher object related to the current validation process
        $voucher_object = Voucher::find($voucher_validation_log_data['voucher_id']);
        $new_balance = $voucher_object->balance - $voucher_validation_log_data['value'];
//        todo update voucher information (status, balance, validation_times, last_validation_date)
        $voucher_validation_log_data['balance'] = $new_balance;
        
//        update voucher status
        if ( 1 ===  (int)$voucher_object->voucherParameter->no_of_uses || 0 >= $new_balance) {
            $new_status = 'validated';
        }else{
            $new_status = 'valid';
        }
        $new_validation_times = (int)$voucher_object->validation_times + 1;
        $voucher_validation_log_data['log'] = $new_status;
        
        DB::beginTransaction();
        if ( $voucher_validation_log_object = VoucherValidationLog::create($voucher_validation_log_data) ) {
            $update_voucher_data = [
                'status' => $new_status,
                'balance' => $new_balance,
                'validation_times' => $new_validation_times,
                'last_validation_date' => $voucher_validation_log_object->created_at
            ];
            $voucher_object->update($update_voucher_data);
            DB::commit();
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
