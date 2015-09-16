<?php

namespace App\Http\Controllers;

//Requests
use Illuminate\Http\Request;
use App\Http\Requests\ValidateVoucherRequest;

//Controllers
use App\Http\Controllers\ApiController;

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
        $voucher_validate_log_data['voucher_id'] = (int)$request->get('data')['relations']['voucher']['voucher_id'];
        $voucher_validate_log_data['value'] = ($request->get('data')['value']);
//        todo continue preparing data ('business_id', 'user_id', 'balance', 'log')
        $voucher_object = Voucher::find($voucher_validate_log_data['voucher_id']);
        $new_balance = $voucher_object->balance - $voucher_validate_log_data['value'];
//        todo update voucher information (status, balance, validation_times, last_validation_date)
        
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
